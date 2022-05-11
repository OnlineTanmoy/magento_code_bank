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
namespace Appseconnect\B2BMage\Controller\QuickOrder\Cart;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class ProductListing
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductListing extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\Page
     */
    public $resultPageFactory;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;
   
    /**
     * Product List constructor
     *
     * @param Context                                 $context           context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory   customer
     * @param Session                                 $customerSession   customer session
     * @param PageFactory                             $resultPageFactory result page
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
    
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Redirect to the Quick Order UI page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerSessionId = $this->customerSession->getCustomerId();
        $customerType = $this->customerFactory->create()
            ->load($this->customerSession->getCustomerId())
            ->getCustomerType();
        if ($customerType == 1) {
            return $this->resultRedirectFactory->create()->setPath('customer/account');
        }
        if (! ($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Quick Order'));
        return $resultPage;
    }
}
