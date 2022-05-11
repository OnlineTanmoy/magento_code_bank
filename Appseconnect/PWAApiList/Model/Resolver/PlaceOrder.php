<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartManagementInterface;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\QuoteGraphQl\Model\Cart\CheckCartCheckoutAllowance;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @inheritdoc
 */
class PlaceOrder extends \ScandiPWA\QuoteGraphQl\Model\Resolver\PlaceOrder
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CheckCartCheckoutAllowance
     */
    private $checkCartCheckoutAllowance;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;
    /**
     * CreditFactory
     *
     * @var \Appseconnect\B2BMage\Model\CreditFactory
     */
    public $creditFactory;
    /**
     * @var \Appseconnect\B2BMage\Model\OrderApproverFactory
     */
    public $orderApproverFactory;
    /**
     * @var \Appseconnect\B2BMage\Helper\Sales\Data
     */
    public $helperSales;

    /**
     * PlaceOrder constructor.
     * @param GetCartForUser $getCartForUser
     * @param CartManagementInterface $cartManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param CheckCartCheckoutAllowance $checkCartCheckoutAllowance
     * @param StoreManagerInterface $storeManager
     * @param CartRepositoryInterface $cartRepository
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit HelperCreditLimit
     * @param \Appseconnect\B2BMage\Model\CreditFactory $creditFactory CreditFactory
     * @param \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory OrderApproverFactory
     * @param \Appseconnect\B2BMage\Helper\Sales\Data $helperSales
     */
    public function __construct(
        GetCartForUser                                  $getCartForUser,
        CartManagementInterface                         $cartManagement,
        OrderRepositoryInterface                        $orderRepository,
        CheckCartCheckoutAllowance                      $checkCartCheckoutAllowance,
        StoreManagerInterface                           $storeManager,
        CartRepositoryInterface                         $cartRepository,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data   $helperCreditLimit,
        \Appseconnect\B2BMage\Model\CreditFactory       $creditFactory,
        \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory,
        \Appseconnect\B2BMage\Helper\Sales\Data         $helperSales
    )
    {
        $this->getCartForUser = $getCartForUser;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->checkCartCheckoutAllowance = $checkCartCheckoutAllowance;
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->creditFactory = $creditFactory;
        $this->orderApproverFactory = $orderApproverFactory;
        $this->helperSales = $helperSales;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $guestCartId = $args['guestCartId'] ?? '';
        $contactPersonId = $context->getUserId();
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        if ($guestCartId !== '') {
            $cart = $this->getCartForUser->execute($guestCartId, $contactPersonId, $storeId);
        } else {
            $cart = $this->cartManagement->getCartForCustomer($contactPersonId);
        }

        $this->checkCartCheckoutAllowance->execute($cart);

        if ((int)$context->getUserId() === 0) {
            if (!$cart->getCustomerEmail()) {
                throw new GraphQlInputException(__("Guest email for cart is missing."));
            }
            $cart->setCheckoutMethod(CartManagementInterface::METHOD_GUEST);
        }

        try {
            $isContactPerson = $this->helperContactPerson->checkCustomerStatus(
                $context->getUserId(),
                true
            );

            $currStoreId = $this->storeManager->getStore()->getId();
            $cart->setStoreId($currStoreId);
            $this->cartRepository->save($cart);

            $orderId = $this->cartManagement->placeOrder($cart->getId());
            $order = $this->orderRepository->get($orderId);

            # credit limit work
            if ($context->getUserId() && $isContactPerson['customer_type'] == 3) {
                $order->setContactPersonId($contactPersonId);
                $contactPersonData = $this->helperContactPerson->getCustomerId($contactPersonId);
                $customerId = $contactPersonData['customer_id'];
                $incrementId = $order->getIncrementId();
                $grandTotal = $order->getGrandTotal();
                $paymentMathod = $order->getPayment()
                    ->getMethodInstance()
                    ->getCode();

                $check = $this->helperCreditLimit->isValidPayment($paymentMathod);
                if ($check) {
                    $customerCreditDetail = $this->helperCreditLimit->getCustomerCreditData($customerId);
                    $creditLimit = $customerCreditDetail['credit_limit'];
                    $availableBalance = $customerCreditDetail['available_balance'];
                    $availableBalance = $availableBalance - $grandTotal;
                    $creditLimitDataArray = [];
                    $creditLimitDataArray['customer_id'] = $customerId;
                    $creditLimitDataArray['credit_limit'] = $creditLimit;
                    $creditLimitDataArray['increment_id'] = $incrementId;
                    $creditLimitDataArray['available_balance'] = $availableBalance;
                    $creditLimitDataArray['debit_amount'] = $grandTotal;
                    $creditModel = $this->creditFactory->create();
                    $creditModel->setData($creditLimitDataArray);
                    $creditModel->save();

                    if ($creditLimit) {
                        $this->helperCreditLimit->saveCreditLimit($customerId, $creditLimit);
                    }
                    if ($availableBalance) {
                        $this->helperCreditLimit->saveCreditBalance($customerId, $availableBalance);
                    }
                }
                $approverId = $this->helperSales->getApproverId($customerId, $grandTotal);
                if ($approverId && ($approverId['contact_person_id'] != $contactPersonId)) {
                    $order->hold()->save();
                    $orderapproverModel = $this->orderApproverFactory->create();
                    $orderapproverModel->setData('increment_id', $incrementId);
                    $orderapproverModel->setData('customer_id', $customerId);
                    $orderapproverModel->setData('contact_person_id', $approverId['contact_person_id']);
                    $orderapproverModel->setData('grand_total', $grandTotal);
                    $orderapproverModel->save();
                }
            }

            return [
                'order' => [
                    'order_id' => $order->getIncrementId(),
                ],
            ];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__('Unable to place order: %message', ['message' => $e->getMessage()]), $e);
        }
    }
}
