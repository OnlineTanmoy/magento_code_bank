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
 * Class Listing
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Listing extends \Magento\Customer\Controller\AbstractAccount
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
     * Listing constractor
     *
     * @param Context                                         $context             context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper contact person helper
     * @param Session                                         $customerSession     customer session
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
     * Listing exiqute
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
        $customerId = $this->customerSession->getCustomer()->getId();
        
        $customerData = $this->contactPersonHelper->checkCustomerStatus($customerId, true);
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Order List For Approval'));
        $block = $resultPage->getLayout()->getBlock('salesorder.approve.listing');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
