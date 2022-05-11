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

use Magento\Customer\Model\Session;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderSetParentCustomerObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderSetParentCustomerObserver implements ObserverInterface
{
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
     * QuoteFactory
     *
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     *  Data
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    protected $quotationHelper;

    /**
     * OrderSetParentCustomerObserver constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper QuotationHelper
     * @param \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory QuoteFactory
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->quotationHelper = $quotationHelper;
        $this->quoteFactory = $quoteFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Overriding the original customer details with its parent customer
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId = null;
        $parentCustomer = null;
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        $order->setQuotationInfo($quote->getQuotationInfo());
        if ($quote->getQuotationInfo()) {
            $quoteDetail = $this->quotationHelper->getQuotationInfo($quote->getQuotationInfo());
            $quote = $this->quoteFactory->create()->load($quoteDetail["id"]);
            $quote->setStatus("closed");
            $quote->save();
        }
        if ($quote->getCustomer()) {
            $quoteData = $observer->getQuote()->getData();
            $customerId = $quoteData["customer_id"];
            $customer = $this->customerFactory->create()->load($customerId);
            if ($this->helperContactPerson->isContactPerson($customer)) {
                if ($this->customerSession->getCurrentCustomerId()) {
                    $customerId = $this->customerSession->getCurrentCustomerId();
                    $parentCustomer = $this->customerFactory->create()->load($customerId);
                } else {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId; //B2B
                    $parentCustomer = $this->customerFactory->create()->load($customerId);
                }
                $order->setCustomerId($customerId);
            }
        }
        if ($parentCustomer) {
            if ($order->getShippingAddress()) {
                $order->getShippingAddress()->setEmail($parentCustomer->getEmail());
            }
            $order->getBillingAddress()->setEmail($parentCustomer->getEmail());

            $order->setCustomerEmail($parentCustomer->getEmail());
            $order->setCustomerFirstname($parentCustomer->getFirstname());
            $order->setCustomerMiddlename($parentCustomer->getMiddlename());
            $order->setCustomerLastname($parentCustomer->getLastname());
        }
    }
}
