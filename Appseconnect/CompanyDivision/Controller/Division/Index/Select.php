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
namespace Appseconnect\CompanyDivision\Controller\Division\Index;

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
class Select extends \Magento\Framework\App\Action\Action implements OrderInterface
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
     * Salesrep listing constructor
     *
     * @param Context     $context           context
     * @param Session     $customerSession   customer session
     * @param PageFactory $resultPageFactory result page
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
    
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }

        $currentCustomerId = $this->getRequest()->getParam('customer_id');
        $this->customerSession->setCurrentCustomerId($currentCustomerId);

        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl('/division/division/index_listing');

        return $redirect;

    }
}
