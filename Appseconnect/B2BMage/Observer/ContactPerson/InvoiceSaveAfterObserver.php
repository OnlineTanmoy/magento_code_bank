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

namespace Appseconnect\B2BMage\Observer\ContactPerson;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class InvoiceSaveAfterObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class InvoiceSaveAfterObserver implements ObserverInterface
{
    /**
     * Invoice
     *
     * @var $invoice
     */
    protected $invoice;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * InvoiceSaveAfterObserver constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data   $helperCreditLimit   HelperCreditLimit
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoiceData = $observer->getEvent()->getData('invoice');
        $paymentMethod = $invoiceData->getOrder()
            ->getPayment()
            ->getMethodInstance()
            ->getCode();

        $customerDuscountPercent = $invoiceData->getOrder()->getCustomerDiscount();
        if ($customerDuscountPercent) {
            $discountAmount = $invoiceData->getSubtotal() * ($customerDuscountPercent / 100);
            $invoiceData->setCustomerDiscountAmount($discountAmount);
            $invoiceData->save();
        }

        $userId = $invoiceData->getOrder()->getCustomerId();
        $check = $this->helperCreditLimit->isValidPayment($paymentMethod);
        if (!$this->invoice && $userId && $check) {
            $this->invoice = $invoiceData->getId();
            $grandTotal = $invoiceData->getGrandTotal();
            $this->helperCreditLimit->creditLimitUpdate(
                $userId,
                $invoiceData->getOrder(),
                $grandTotal
            );
        }
    }
}
