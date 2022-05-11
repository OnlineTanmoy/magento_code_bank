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
 * Class OrderCancelAfterObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderCancelAfterObserver implements ObserverInterface
{

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
     * OrderCancelAfterObserver constructor.
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
        $orderData = $observer->getEvent()->getData('order');
        $paymentMethod = $orderData
            ->getPayment()
            ->getMethodInstance()
            ->getCode();

        $userId = $orderData->getCustomerId();
        $check = $this->helperCreditLimit->isValidPayment($paymentMethod);
        if ($userId && $check) {
            $grandTotal = $orderData->getGrandTotal();
            $this->helperCreditLimit->creditLimitUpdate(
                $userId,
                $orderData,
                $grandTotal
            );
        }
    }
}
