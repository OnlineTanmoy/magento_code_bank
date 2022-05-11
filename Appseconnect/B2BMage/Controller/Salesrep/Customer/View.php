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
namespace Appseconnect\B2BMage\Controller\Salesrep\Customer;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;
    
    /**
     * View constructor
     *
     * @param Context                                         $context             context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper contact person helper
     * @param Session                                         $customerSession     customer session helper
     * @param PageFactory                                     $resultPageFactory   result page
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
    
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->contactPersonHelper = $contactPersonHelper;
        parent::__construct($context);
    }
   
    /**
     * View exiqute
     *
     * @return PageFactory
     */
    public function execute()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        
        $customerId = $this->getRequest()->getParam('customer_id');
        $customerId = $customerId;
        $customerData = $this->contactPersonHelper->checkCustomerStatus($customerId, true);
        $resultPage = $this->resultPageFactory->create();
        
        if ($customerData) {
            $resultPage->getConfig()
                ->getTitle()
                ->set(__($customerData['firstname'] . ' ' . $customerData['lastname']));
        }
        
        $block = $resultPage->getLayout()->getBlock('salesrepresentative.customer.view');
        
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        
        if ($navigationBlock) {
            $navigationBlock->setActive('b2bmage/salesrep/customer_listing');
        }
        return $resultPage;
    }
}
