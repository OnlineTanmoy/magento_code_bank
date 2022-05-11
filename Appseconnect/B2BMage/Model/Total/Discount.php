<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Model\Total;

use Magento\Customer\Model\Session;
use Magento\Quote\Model\Quote\Address;

/**
 * Class Discount
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Contact peron helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customer;

    /**
     * Discount constructor.
     *
     * @param Session                                         $session             session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param \Magento\Customer\Model\CustomerFactory         $customer            customer
     */
    public function __construct(
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customer
    ) {
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->customer = $customer;
    }

    /**
     * Clear values
     *
     * @param Address\Total $total total
     *
     * @return void
     */
    public function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote quote
     * @param Address\Total              $total total
     *
     * @return array
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        Address\Total $total
    ) {

        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            $customerSpecificDiscount = 0;
            $customerType = $this->customerSession->getCustomer()->getCustomerType();
            if ($customerType == 1) {
                $customerSpecificDiscount = $this->customerSession
                    ->getCustomer()->getCustomerSpecificDiscount();
            }
            if ($customerType == 3) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
                $customerCollection = $this->customer->create()->load($customerDetail['customer_id']);
                $customerSpecificDiscount = $customerCollection->getCustomerSpecificDiscount();
            }
            if ($customerType == 2) {
                $customerSpecificDiscount = 0;
            }
            return [
                'code' => 'customer_discount',
                'title' => $this->getLabel($customerSpecificDiscount),
                'value' => $quote->getSubtotal() * ($customerSpecificDiscount / 100)
            ];
        } else {
            return [
                'code' => 'customer_discount',
                'title' => $this->getLabel(),
                'value' => 'Not yet calculated'
            ];
        }
    }

    /**
     * Get label
     *
     * @param int $customerSpecificDiscount customer specific discount
     *
     * @return mixed
     */
    public function getLabel($customerSpecificDiscount = 0)
    {
        if ($customerSpecificDiscount > 0) {
            return __('Customer Discount ( ' . $customerSpecificDiscount . '% )');
        } else {
            return __('Customer Discount');
        }
    }
}
