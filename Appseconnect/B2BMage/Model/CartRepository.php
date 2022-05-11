<?php
/**
 * Namespace
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Appseconnect\B2BMage\Api\ContactPerson\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Api\SortOrder;

/**
 * Class CartRepository
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CartRepository implements CartRepositoryInterface
{
    /**
     * QuoteManagement
     *
     * @var QuoteManagement
     */
    public $quoteManagement;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CartItemRepositoryInterface
     *
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    public $cartItemRepository;

    /**
     * CartRepositoryInterface
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    public $cartRepository;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\Price\CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * ItemFactory
     *
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    public $cartItemFactory;

    /**
     * ShippingMethodManagementInterface
     *
     * @var \Magento\Quote\Api\ShippingMethodManagementInterface
     */
    public $shippingMethodManagement;

    /**
     * BillingAddressManagement
     *
     * @var \Magento\Quote\Model\BillingAddressManagement
     */
    public $billingAddressManagement;

    /**
     * PaymentMethodManagementInterface
     *
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    public $paymentMethodManagement;

    /**
     * ShippingInformationManagement
     *
     * @var Magento\Checkout\Model\ShippingInformationManagement
     */
    public $shippingInformationManagement;

    /**
     * PaymentInformationManagementInterface
     *
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    public $paymentInformationManagement;

    /**
     * CollectionFactory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * CustomerSearchResultsInterfaceFactory
     *
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * OrderRepositoryInterface
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * OrderFactory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;

    /**
     * UserContextInterface
     *
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    public $userContext;

    /**
     * CouponManagement
     *
     * @var \Magento\Quote\Model\CouponManagement
     */
    public $couponManagement;

    /**
     * QuoteFactory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * CartTotalManagementInterface
     *
     * @var \Magento\Quote\Api\CartTotalManagementInterface
     */
    public $cartTotalManagement;

    /**
     * CreditFactory
     *
     * @var CreditFactory
     */
    public $creditFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;

    /**
     * CartRepository constructor.
     *
     * @param QuoteManagement                                                  $quoteManagement                 QuoteManagement
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                  $helperContactPerson             HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory                 CustomerFactory
     * @param \Magento\Quote\Model\Quote\Item\Repository                       $cartItemRepository              CartItemRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface                       $cartRepository                  CartRepository
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data                      $helperPriceRule                 HelperPriceRule
     * @param ResourceModel\Price\CollectionFactory                            $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory                           $cartItemFactory                 CartItemFactory
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface             $shippingMethodManagement        ShippingMethodManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface              $paymentMethodManagement         PaymentMethodManagement
     * @param \Magento\Quote\Api\BillingAddressManagementInterface             $billingAddressManagement        BillingAddressManagement
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface     $shippingInformationManagement   ShippingInformationManagement
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface      $paymentInformationManagement    PaymentInformationManagement
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory       $orderCollectionFactory          OrderCollectionFactory
     * @param \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory            SearchResultsFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface                      $orderRepository                 OrderRepository
     * @param \Magento\Sales\Model\OrderFactory                                $orderFactory                    OrderFactory
     * @param \Magento\Authorization\Model\UserContextInterface                $userContext                     UserContext
     * @param \Magento\Quote\Model\CouponManagement                            $couponManagement                CouponManagement
     * @param \Magento\Quote\Model\QuoteFactory                                $quoteFactory                    QuoteFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface                  $cartTotalManagement             CartTotalManagement
     * @param CreditFactory                                                    $creditFactory                   CreditFactory
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data                    $helperCreditLimit               HelperCreditLimit
     */
    public function __construct(
        QuoteManagement $quoteManagement,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\Quote\Item\Repository $cartItemRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Quote\Model\Quote\ItemFactory $cartItemFactory,
        \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Quote\Model\CouponManagement $couponManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalManagement,
        \Appseconnect\B2BMage\Model\CreditFactory $creditFactory,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartRepository = $cartRepository;
        $this->helperPriceRule = $helperPriceRule;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->userContext = $userContext;
        $this->couponManagement = $couponManagement;
        $this->quoteFactory = $quoteFactory;
        $this->cartTotalManagement = $cartTotalManagement;
        $this->creditFactory = $creditFactory;
        $this->helperCreditLimit = $helperCreditLimit;
    }

    /**
     * CreateEmptyCartForCustomer
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createEmptyCartForCustomer($contactPersonId)
    {
        return $this->quoteManagement->createEmptyCartForCustomer($contactPersonId);
    }

    /**
     * AddCartItem
     *
     * @param int                                       $contactPersonId ContactPersonId
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem        CartItem
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addCartItem($contactPersonId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quoteItem = $this->cartItemRepository->save($cartItem);

        $this->priceCalculation($quoteItem, $contactPersonId);

        return $quoteItem;
    }

    /**
     * DeleteById
     *
     * @param int $contactPersonId ContactPersonId
     * @param int $itemId          ItemId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($contactPersonId, $itemId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {
            $result = $this->cartItemRepository->deleteById($quote->getId(), $itemId);
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }

        return $result;
    }

    /**
     * GetCart
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCart($contactPersonId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        try {
            $quote = $this->cartRepository->getForCustomer($contactPersonId);

            if ($quote) {
                return $this->cartRepository->get($quote->getId());
            } else {
                return;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Cart Not exist")
            );
        }
    }

    /**
     * PriceCalculation
     *
     * @param $cartItem        CartItem
     * @param $contactPersonId ContactPersonId
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function priceCalculation($cartItem, $contactPersonId)
    {

        $customerDetail = $this->helperContactPerson->getCustomerId($contactPersonId);
        $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
        $customerPricelistCode = $customerCollection->getData('pricelist_code');
        $customerId = $customerDetail['customer_id'];

        $websiteId = $customerCollection->getWebsiteId();

        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        if ($customerId) {
            $quote = $this->cartRepository->get($cartItem->getQuoteId());
            $quoteItem = '';
            foreach ($quote->getAllItems() as $item) {
                if ($item->getId() == $cartItem->getId()) {
                    $quoteItem = $item;
                    break;
                }
            }
            if ($cartItem->getProductType() == 'bundle') {
                $this->helperPriceRule->processBundleProduct(
                    $quoteItem,
                    $customerPricelistCode,
                    $pricelistStatus,
                    $customerId,
                    $websiteId
                );
            } elseif ($cartItem->getProductType() == 'configurable') {
                $this->helperPriceRule->processConfigurableProduct(
                    $quoteItem,
                    $customerPricelistCode,
                    $pricelistStatus,
                    $customerId,
                    $websiteId
                );
            } else {
                $this->helperPriceRule->processSimpleProduct(
                    $quoteItem,
                    $customerPricelistCode,
                    $pricelistStatus,
                    $customerId,
                    $websiteId
                );
            }

            $quoteItem->save();
            $quote->collectTotals()->save();
        }
    }

    /**
     * GetShippingMethod
     *
     * @param int $contactPersonId ContactPersonId
     * @param int $addressId       The estimate address id D
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]|void
     */
    public function getShippingMethod($contactPersonId, $addressId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {
            $shippingMethod = $this->shippingMethodManagement->estimateByAddressId($quote->getId(), $addressId);
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }

        return $shippingMethod;
    }

    /**
     * GetPaymentMethod
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentMethod($contactPersonId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {
            $paymentMethod = $this->paymentMethodManagement->getList($quote->getId());
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }

        return $paymentMethod;
    }

    /**
     * AssignBilling
     *
     * @param int                                      $contactPersonId ContactPersonId
     * @param \Magento\Quote\Api\Data\AddressInterface $address         Address
     * @param bool                                     $useForShipping  UseForShipping
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignBilling($contactPersonId, $address, $useForShipping = false)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {
            $addressId = $this->billingAddressManagement->assign($quote->getId(), $address, $useForShipping);
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }

        return $addressId;
    }

    /**
     * SaveShippinginformation
     *
     * @param int                                                     $contactPersonId    ContactPersonId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation AddressInformation
     *
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveShippinginformation(
        $contactPersonId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {
            $paymentInformation = $this->shippingInformationManagement->saveAddressInformation($quote->getId(), $addressInformation);
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }
        return $paymentInformation;
    }

    /**
     * SavePaymentInformationAndPlaceOrder
     *
     * @param int                                           $contactPersonId ContactPersonId
     * @param \Magento\Quote\Api\Data\PaymentInterface      $paymentMethod   PaymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress  BillingAddress
     *
     * @return int|void
     */
    public function savePaymentInformationAndPlaceOrder(
        $contactPersonId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        if ($quote) {

            $orderId = $this->paymentInformationManagement->savePaymentInformationAndPlaceOrder($quote->getId(), $paymentMethod, $billingAddress);
            $order = $this->orderFactory->create()->load($orderId);
            $order->setContactPersonId($contactPersonId);
            if ($this->userContext->getUserId() != $contactPersonId) {
                $order->setSalesrepId($this->userContext->getUserId());
                $order->setIsPlacedbySalesrep(1);
            }
            $order->save();

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
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person active cart not exist", $contactPersonId)
            );
        }
        return $orderId;
    }


    /**
     * GetOrders
     *
     * @param int                                            $contactPersonId ContactPersonId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria  SearchCriteria
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrders($contactPersonId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $customerDetail = $this->helperContactPerson->getCustomerId($contactPersonId);
        $customerId = $customerDetail['customer_id'];

        $collection = $this->orderCollectionFactory->create()->addAttributeToSelect('*')
            ->addAttributeToFilter('contact_person_id', array('eq' => $contactPersonId));

        if ($searchCriteria) {
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            // Add filters from root filter group to the collection
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $result = $this->addFilterCustomerData($group, $collection);
            }

            $searchResults->setTotalCount($collection->getSize());
            $sortOrders = $searchCriteria->getSortOrders();
            if ($sortOrders) {
                foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                    $collection
                        ->addOrder(
                            $sortOrder->getField(),
                            ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                        );
                }
            }
            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        $orders = array();
        foreach ($collection as $orderModel) {
            $orders[] = $this->orderRepository->get($orderModel->getId());
        }
        $searchResults->setItems($orders);

        return $searchResults;
    }

    /**
     * GetOrderById
     *
     * @param int $contactPersonId ContactPersonId
     * @param int $orderId         OrderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderById($contactPersonId, $orderId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $order = $this->orderRepository->get($orderId);


        return $order;
    }

    /**
     * SetCouponCode
     *
     * @param int    $contactPersonId ContactPersonId
     * @param string $couponCode      CouponCode
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setCouponCode($contactPersonId, $couponCode)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }

        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        return $this->couponManagement->set($quote->getId(), $couponCode);
    }

    /**
     * DeleteCouponCode
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteCouponCode($contactPersonId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        return $this->couponManagement->remove($quote->getId());
    }

    /**
     * GetCouponCode
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCouponCode($contactPersonId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        return $this->couponManagement->get($quote->getId());
    }

    /**
     * GetTotal
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface|void
     */
    public function getTotal($contactPersonId)
    {
        $customer = $this->customerFactory->create()->load($contactPersonId);
        if (!$this->helperContactPerson->isContactPerson($customer)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request Contact Person doesn't exist", $contactPersonId)
            );
        }
        $quote = $this->cartRepository->getActiveForCustomer($contactPersonId);

        return $this->cartTotalManagement->get($quote->getId());
    }
}
