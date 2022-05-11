<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Observer\CustomerSpecificDiscount;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderPlaceAfterObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderPlaceAfterObserver implements ObserverInterface
{
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * OrderPlaceAfterObserver constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     CustomerFactory
     * @param \Magento\Framework\App\ResourceConnection       $resources           Resources
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resources,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    ) {
        $this->customerFactory = $customerFactory;
        $this->resources = $resources;
        $this->helperContactPerson = $helperContactPerson;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer Observer
     *
     * @return $this @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $customerCollection = $this->customerFactory->create()->load($customerId);
        $customerSpecificDiscount = $customerCollection->getCustomerSpecificDiscount();
        $orderIncrementId = $order->getIncrementId();
        $customerType = $customerCollection->getCustomerType();
        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
            $customerSpecificDiscount = $customerCollection->getCustomerSpecificDiscount();
        }
        if ($customerType == 2) {
            $customerSpecificDiscount = 0;
        }
        if (! $customerSpecificDiscount) {
            $customerSpecificDiscount = 0;
        }
        $data = [
            'customer_discount' => $customerSpecificDiscount,
            'customer_discount_amount' => $order->getSubtotal() * ( $customerSpecificDiscount / 100 )
        ];
        $connection = $this->resources->getConnection();
        $where['increment_id = ?'] = $orderIncrementId;
        $orderTable = $this->resources->getTableName('sales_order');
        $connection->update($orderTable, $data, $where);
        return $this;
    }
}
