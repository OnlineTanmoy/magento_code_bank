<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Approver;

use Appseconnect\B2BMage\Model\ResourceModel\OrderApprover\Collection;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Appseconnect\B2BMage\Model\ResourceModel\OrderApproverFactory;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;

/**
 * Orders data reslover
 */
class GetApprovalOrderList implements ResolverInterface
{
    /**
     * Order Approver
     *
     * @var OrderApproverFactory
     */
    public $orderApproverResourceFactory;
    /**
     * Order
     *
     * @var OrderCollection
     */
    public $orders;
    /**
     * Order collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;
    /**
     * Order config
     *
     * @var OrderConfig
     */
    public $orderConfig;
    /**
     * @var CollectionFactoryInterface
     */
    private $collectionFactory;

    /**
     * @param CollectionFactoryInterface $collectionFactory
     * @param OrderApproverFactory $orderApproverResourceFactory
     * @param OrderConfig $orderConfig
     * @param OrderCollection $orderCollectionFactory
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        OrderApproverFactory $orderApproverResourceFactory,
        OrderConfig $orderConfig,
        OrderCollection $orderCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\Sales\Data $approverHelper,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderApproverResourceFactory = $orderApproverResourceFactory;
        $this->orderConfig = $orderConfig;
        $this->customerFactory = $customerFactory;
        $this->approverHelper = $approverHelper;
        $this->contactHelper = $contactHelper;
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
        $items = [];
        $customerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($customerId);

        if ($this->contactHelper->isContactPerson($customer)) {
            $b2bCustomerId = $this->contactHelper->getCustomerId($customerId)['customer_id'];
            $b2bCustomerStatus = $this->customerFactory->create()
                ->load($b2bCustomerId)
                ->getCustomerStatus();
            $isApprover = $this->approverHelper->isApprover($customerId);

            if ($isApprover && ($customer['is_active'] && $b2bCustomerStatus) && !$this->orders) {
                $orderApproverResource = $this->orderApproverResourceFactory->create();
                $this->orders = $this->orderCollectionFactory->create()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter(
                        'main_table.status', [
                            'in' => $this->orderConfig->getVisibleOnFrontStatuses()
                        ]
                    )
                    ->setOrder('main_table.created_at', 'desc');

                $approvalOrders = $orderApproverResource->getApprovalOrders(
                    $customerId,
                    $this->orders
                );
                $approvalOrders->addFieldToFilter('main_table.status', 'holded');
                $orders = $approvalOrders;
                foreach ($orders as $order) {
                    $trackNumbers = [];
                    $tracksCollection = $order->getTracksCollection();
                    foreach ($tracksCollection->getItems() as $track) {
                        $trackNumbers[] = $track->getTrackNumber();
                    }

                    $shippingInfo = [
                        'shipping_amount' => $order->getShippingAmount(),
                        'shipping_address'=> $order->getShippingAddress(),
                        'shipping_method' => $order->getShippingMethod(),
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

                    $items[] = [
                        'base_order_info' => $base_info,
                        'shipping_info' => $shippingInfo,
                        'payment_info' => $order->getPayment()->getData()
                    ];
                }
                return ['items' => $items];
            }
        }
    }
}
