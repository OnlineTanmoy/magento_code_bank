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

namespace Appseconnect\B2BMage\Observer\Sales;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogBlockProductListCollection
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AfterOrderObserver implements ObserverInterface
{

    /**
     * Session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * CreditFactory
     *
     * @var \Appseconnect\B2BMage\Model\CreditFactory
     */
    public $creditFactory;

    /**
     * OrderApproverFactory
     *
     * @var \Appseconnect\B2BMage\Model\OrderApproverFactory
     */
    public $orderApproverFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Sales\Data
     */
    public $helperSales;

    /**
     * AfterOrderObserver constructor.
     *
     * @param Session                                          $session              Session
     * @param \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory OrderApproverFactory
     * @param \Appseconnect\B2BMage\Model\CreditFactory        $creditFactory        CreditFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data  $helperContactPerson  HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data    $helperCreditLimit    HelperCreditLimit
     * @param \Appseconnect\B2BMage\Helper\Sales\Data          $helperSales          HelperSales
     */
    public function __construct(
        Session $session,
        \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory,
        \Appseconnect\B2BMage\Model\CreditFactory $creditFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit,
        \Appseconnect\B2BMage\Helper\Sales\Data $helperSales
    ) {
        $this->customerSession = $session;
        $this->creditFactory = $creditFactory;
        $this->orderApproverFactory = $orderApproverFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->helperSales = $helperSales;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('order');
        $item->setSalesrepId(
            $this->getCatalogSession()
                ->getSalesrepId()
        );
        if ($this->getCatalogSession()->getSalesrepId()) {
            $item->setIsPlacedbySalesrep(1);
        }

        $isContactPerson = $this->helperContactPerson->checkCustomerStatus(
            $this->customerSession->getData('customer_id'),
            true
        );

        if ($this->customerSession->isLoggedIn() && $isContactPerson['customer_type'] == 3) {
            $item->setContactPersonId($this->customerSession->getCustomer()->getId());
            $contactPersonId = $this->customerSession->getData('customer_id');
            $contactPersonData = $this->helperContactPerson->getCustomerId($contactPersonId);
            $customerId = $contactPersonData['customer_id'];
            $incrementId = $item->getIncrementId();
            $grandTotal = $item->getGrandTotal();
            $paymentMathod = $item->getPayment()
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

            $approverId = $this->helperSales->getApproverId($customerId, $grandTotal);

            $salesrepId = $this->getCatalogSession()->getSalesrepId();

            if ($approverId && ($approverId['contact_person_id'] != $contactPersonId || $salesrepId)) {
                $item->hold();

                $orderapproverModel = $this->orderApproverFactory->create();
                $orderapproverModel->setData('increment_id', $incrementId);
                $orderapproverModel->setData('customer_id', $customerId);
                $orderapproverModel->setData('contact_person_id', $approverId['contact_person_id']);
                $orderapproverModel->setData('grand_total', $grandTotal);
                $orderapproverModel->save();
            }
        }

        return $this;
    }

    /**
     * Get CatalogSession
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        if (!$this->catalogSession) {
            $this->catalogSession = ObjectManager::getInstance()->get(\Magento\Catalog\Model\Session::class);
        }
        return $this->catalogSession;
    }
}
