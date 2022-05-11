<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Sales\Approve;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Order
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Order extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Order
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;
    
    /**
     * Order approver
     *
     * @var \Appseconnect\B2BMage\Model\OrderApproverFactory
     */
    public $orderApproverFactory;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Credit limit helper
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * Sales email helper
     *
     * @var \Appseconnect\B2BMage\Helper\Sales\Email
     */
    public $helperSalesEmail;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Order Constractor
     *
     * @param Context                                            $context              context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig          scope config
     * @param \Magento\Customer\Model\CustomerFactory            $customerFactory      customer
     * @param \Appseconnect\B2BMage\Helper\Sales\Email           $helperSalesEmail     helper sales email
     * @param \Magento\Sales\Model\OrderFactory                  $orderFactory         order
     * @param \Appseconnect\B2BMage\Model\OrderApproverFactory   $orderApproverFactory order approver
     * @param Session                                            $customerSession      customer session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data    $helperContactPerson  contact person helper
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data      $helperCreditLimit    credit limit helper
     * @param PageFactory                                        $resultPageFactory    result page
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\Sales\Email $helperSalesEmail,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit,
        PageFactory $resultPageFactory
    ) {
    
        $this->orderFactory = $orderFactory;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->helperSalesEmail = $helperSalesEmail;
        $this->orderApproverFactory = $orderApproverFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    /**
     * Order exiqute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $status = $this->getRequest()->getParam('status');
        if (! ($customerSessionId = $this->customerSession->getCustomerId()) 
            || ! isset($status)
        ) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        $orderId = $this->getRequest()->getParam('order_id');
        $approveId = $this->getRequest()->getParam('approve_id');
        
        if (isset($status)) {
            $orderApproverModel = $this->orderApproverFactory->create()->load($approveId);
            $order = $this->orderFactory->create()->load($orderId);
            if ($status == 'cancel') {
                $orderApproverModel->setStatus('Canceled');
                $this->messageManager->addSuccess(__('Order has been canceled.'));
                $this->_sendApprovalMail('cancel', $order);
                $order->setStatus('canceled');
                $order->setState('canceled');
                $userId = $order->getData('contact_person_id');
                
                $paymentMethod = $order->getPayment()
                    ->getMethodInstance()
                    ->getCode();
                
                $contactPersonData = $this->helperContactPerson->getCustomerId($userId);
                
                $chack = $this->helperCreditLimit->isValidPayment($paymentMethod);
                
                if (! empty($contactPersonData) && $chack) {
                    $customerId = $contactPersonData['customer_id'];
                    $customerCreditDetail = $this->helperCreditLimit->creditLimitUpdate(
                        $customerId,
                        $order,
                        $order->getData('grand_total')
                    );
                }
            }
            if ($status == 'approve') {
                $orderApproverModel->setStatus('Approved');
                $this->messageManager->addSuccess(__('Order has been approved.'));
                $this->_sendApprovalMail('approved', $order);
                $order->unhold();
            }
            $order->save();
            
            $orderApproverModel->save();
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/approve_listing');
        return $resultRedirect;
    }
    
    /**
     * Send approval mail
     *
     * @param string                     $action action
     * @param \Magento\Sales\Model\Order $order  order
     *
     * @return void
     */
    private function _sendApprovalMail($action, $order)
    {
        $senderName = $this->scopeConfig->getValue('trans_email/ident_sales/name', 'store');
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_sales/email', 'store');
        
        $senderInfo = [
            'name' => $senderName,
            'email' => $senderEmail
        ];
        
        $customer = $this->customerFactory->create()->load($order->getData('customer_id'));
        
        $receiverInfo = [
            'name' => $customer->getName(),
            'email' => $customer->getEmail()
        ];
        $emailTemplateVariables = [];
        $emailTempVariables['customer'] = $customer;
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
