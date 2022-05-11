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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;

/**
 * Class CatalogBlockProductListCollection
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CheckoutObserver implements ObserverInterface
{
    
    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Sales\Data
     */
    public $helperSales;
    
    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * Email
     *
     * @var \Appseconnect\B2BMage\Helper\Sales\Email
     */
    public $helperSalesEmail;

    /**
     * OrderInterface
     *
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    public $order;

    /**
     * CheckoutObserver constructor.
     *
     * @param Session                                            $session             Session
     * @param \Magento\Customer\Model\CustomerFactory            $customerFactory     CustomerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig         ScopeConfig
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data    $helperContactPerson HelperContactPerson
     * @param \Magento\Sales\Api\Data\OrderInterface             $order               Order
     * @param \Appseconnect\B2BMage\Helper\Sales\Email           $helperSalesEmail    HelperSalesEmail
     * @param \Appseconnect\B2BMage\Helper\Sales\Data            $helperSales         HelperSales
     */
    public function __construct(
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Appseconnect\B2BMage\Helper\Sales\Email $helperSalesEmail,
        \Appseconnect\B2BMage\Helper\Sales\Data $helperSales
    ) {
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperSales = $helperSales;
        $this->scopeConfig = $scopeConfig;
        $this->helperSalesEmail = $helperSalesEmail;
        $this->order = $order;
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
        $orderids = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderids[0]);
        $isContactPerson = $this->helperContactPerson
            ->checkCustomerStatus($this->customerSession->getData('customer_id'), true);
        if ($this->customerSession->isLoggedIn() && $isContactPerson['customer_type'] == 3) {
            $contactPersonId = $this->customerSession->getData('customer_id');
            $contactPersonData = $this->helperContactPerson->getCustomerId($contactPersonId);
            $customerId = $contactPersonData['customer_id'];
            $grandTotal = $order->getGrandTotal();
            $approverId = $this->helperSales->getApproverId($customerId, $grandTotal);
            if ($approverId && $approverId['contact_person_id'] != $contactPersonId) {
                $senderName = $this->scopeConfig->getValue('trans_email/ident_sales/name', 'store');
                $senderEmail = $this->scopeConfig->getValue('trans_email/ident_sales/email', 'store');
                
                $senderInfo = [
                    'name' => $senderName,
                    'email' => $senderEmail
                ];
                
                $approverDetail = $this->customerFactory->create()->load($approverId['contact_person_id']);
                
                $receiverInfo = [
                    'name' => $approverDetail->getName(),
                    'email' => $approverDetail->getEmail()
                ];
                $action = 'approval_start';
                $emailTemplateVariables = [];
                $emailTempVariables['approver'] = $approverDetail;
                $emailTempVariables['order'] = $order;
                $emailTempVariables['increment_id'] = $order->getData('increment_id');
                $emailTempVariables['created_at'] = $order->getData('created_at');
                
                $this->helperSalesEmail->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo,
                    $action
                );
            }
        }
    }
}
