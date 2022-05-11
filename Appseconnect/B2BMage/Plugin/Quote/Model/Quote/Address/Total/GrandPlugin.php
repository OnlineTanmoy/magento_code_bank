<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Plugin\Quote\Model\Quote\Address\Total;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GrandPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class GrandPlugin
{

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

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
     * Initialize class variable
     *
     * @param Session                                         $customerSession     CustomerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     CustomerFactory
     */
    public function __construct(
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }

    /**
     * AroundCollect
     *
     * @param \Magento\Quote\Model\Quote\Address\Total\Grand      $subject            Subject
     * @param \Closure                                            $proceed            Proceed
     * @param \Magento\Quote\Model\Quote                          $quote              Quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment ShippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total              Total
     *
     * @return \Magento\Quote\Model\Quote\Address\Total\Grand|mixed
     */
    public function aroundCollect(
        \Magento\Quote\Model\Quote\Address\Total\Grand $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $customerSpecificDiscount = 0.00;
        $discount = 0.00;
        $grandTotal = $total->getGrandTotal();
        $baseGrandTotal = $total->getBaseGrandTotal();
        $totals = array_sum($total->getAllTotalAmounts());
        $baseTotals = array_sum($total->getAllBaseTotalAmounts());

        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            $customerType = $this->customerSession->getCustomer()->getCustomerType();
            if ($customerType == 1) {
                $customerSpecificDiscount = $this->customerSession->getCustomer()->getCustomerSpecificDiscount();
            }
            if ($customerType == 3) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
                $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
                $customerSpecificDiscount = $customerCollection->getCustomerSpecificDiscount();
            }
            if ($customerType == 2) {
                $customerSpecificDiscount = 0;
            }

            if ($customerSpecificDiscount != 0) {
                $discount = $total->getSubtotal() * ($customerSpecificDiscount / 100);
            }

            $total->setGrandTotal($grandTotal + $totals - $discount);
            $total->setBaseGrandTotal($baseGrandTotal + $baseTotals - $discount);

            return $subject;
        } else {
            return $proceed($quote, $shippingAssignment, $total);
        }
    }
}
