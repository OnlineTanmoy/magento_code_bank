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

namespace Appseconnect\B2BMage\Observer\CreditLimit;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderCommitSaveAfterObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SalesOrderCommitSaveAfterObserver implements ObserverInterface
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
     * SalesOrderCommitSaveAfterObserver constructor.
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
        $order = $observer->getEvent()->getOrder('customer');
        $order = $observer->getEvent()->getOrder();
        $userId = $order->getData('customer_id');

        $paymentMathod = $order->getPayment()
            ->getMethodInstance()
            ->getCode();
        $status = $order->getState();

        $contactPersonData = $this->helperContactPerson->getCustomerId($userId);
        $totalCanceled = $order->getData('total_canceled');

        $check = $this->helperCreditLimit->isValidPayment($paymentMathod);

        if (!empty($contactPersonData) && $check && $totalCanceled > 0) {
            $customerId = $contactPersonData['customer_id'];
            $customerCreditDetail = $this->helperCreditLimit->creditLimitUpdate(
                $customerId,
                $order,
                $order->getData('grand_total'),
                $totalCanceled
            );
        }
    }
}
