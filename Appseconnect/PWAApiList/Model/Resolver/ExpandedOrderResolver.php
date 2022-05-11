<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use ScandiPWA\QuoteGraphQl\Model\Customer\CheckCustomerAccount;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Sales\Model\OrderRepository;

/**
 * Orders data resolver
 */
class ExpandedOrderResolver extends \ScandiPWA\QuoteGraphQl\Model\Resolver\ExpandedOrderResolver
{
    /**
     * @var CollectionFactoryInterface
     */
    protected $collectionFactory;

    /**
     * @var CheckCustomerAccount
     */
    protected $checkCustomerAccount;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @param CollectionFactoryInterface $collectionFactory
     * @param CheckCustomerAccount $checkCustomerAccount
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        CheckCustomerAccount $checkCustomerAccount,
        OrderRepository $orderRepository,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->checkCustomerAccount = $checkCustomerAccount;
        $this->orderRepository = $orderRepository;
        $this->contactHelper = $contactHelper;
        $this->customerFactory = $customerFactory;
        parent::__construct($collectionFactory, $checkCustomerAccount, $orderRepository);
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $itemsData = [];
        $trackNumbers = [];

        $customer = $this->customerFactory->create()->load($context->getUserId());

        if (!$this->contactHelper->isContactPerson($customer)) {
            $customerId = $context->getUserId();
            $this->checkCustomerAccount->execute($customerId, $context->getUserType());
        } else {
            $customerId = $this->contactHelper->getContactCustomerId($context->getUserId());
        }

        $orderId = $args['id'];
        $order = $this->orderRepository->get($orderId);

        if ($customerId != $order->getCustomerId()) {
            throw new GraphQlNoSuchEntityException(__('Customer ID is invalid.'));
        }

        foreach ($order->getAllVisibleItems() as $item) {
            $itemsData[] = $item;
        }

        $tracksCollection = $order->getTracksCollection();

        foreach ($tracksCollection->getItems() as $track) {
            $trackNumbers[] = $track->getTrackNumber();
        }

        $shippingInfo = [
            'shipping_amount' => $order->getShippingAmount(),
            'shipping_incl_tax' => $order->getShippingInclTax(),
            'shipping_method' => $order->getShippingMethod(),
            'shipping_address' => $order->getShippingAddress(),
            'shipping_description' => $order->getShippingDescription(),
            'tracking_numbers' => $trackNumbers
        ];

        $base_info = [
            'id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'created_at' => $order->getCreatedAt(),
            'grand_total' => $order->getGrandTotal(),
            'sub_total' => $order->getSubtotalInclTax(),
            'currency_code' => $order->getOrderCurrencyCode(),
            'status' => $order->getStatus(),
            'status_label' => $order->getStatusLabel(),
            'total_qty_ordered' => $order->getTotalQtyOrdered(),
        ];

        return [
            'base_order_info' => $base_info,
            'shipping_info' => $shippingInfo,
            'payment_info' => $order->getPayment()->getData(),
            'products' => $itemsData
        ];
    }
}
