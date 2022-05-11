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
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;

/**
 * Class History
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class History extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * History constructor
     *
     * @param Context                                         $context             context
     * @param Session                                         $customerSession     customer session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param PageFactory                                     $resultPageFactory   result page
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuote,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperQuote = $helperQuote;
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
        $customerSessionId = $this->customerSession->getCustomerId();
        if (! ($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        if($this->helperQuote->getEnableQuoteValue() == 0){
            return $this->resultRedirectFactory->create()->setPath('customer/account');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('My Quotes'));
        return $resultPage;
    }
}
