<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\CustomerSpecificDiscount\Sales\Order\Creditmemo\Email;

/**
 * Interface Totals
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Magento\Sales\Block\Order\Creditmemo\Totals
{
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Resource
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Totals constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     customer
     * @param \Magento\Framework\App\ResourceConnection       $resources           resource
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
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
     * Init total
     *
     * @return $this
     */
    public function initTotals()
    {
        $creditmemoTotalsBlock = $this->getParentBlock();
        $creditmemo = $creditmemoTotalsBlock->getSource();
        $customerId = $creditmemo->getCustomerId();
        $customerCollection = $this->customerFactory->create()->load($customerId);
        $customerSpecificDiscount = $customerCollection->getCustomerSpecificDiscount();

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

        if ($customerSpecificDiscount > 0) {
            $amount = new \Magento\Framework\DataObject(
                [
                'code'       => 'customer_discount',
                'value' => $creditmemo->getSubtotal() * ( $customerSpecificDiscount / 100 ),
                'label' => __('Customer Discount( ' . $customerSpecificDiscount . '% )'),
                'base_value' => $creditmemo->getSubtotal() * ( $customerSpecificDiscount / 100 ),
                ]
            );

            $creditmemoTotalsBlock->addTotal($amount, 'customer_discount');
            $creditmemoTotalsBlock->addTotal($amount, 'customer_discount');
        }

        return $this;
    }

}
