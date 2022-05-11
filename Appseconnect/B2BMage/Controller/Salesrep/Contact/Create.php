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
namespace Appseconnect\B2BMage\Controller\Salesrep\Contact;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class Create
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Create extends \Magento\Framework\App\Action\Action
{
    /**
     * Result Page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Create constructor.
     *
     * @param Context                                    $context           context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory result page
     * @param Session                                    $customerSession   customer session
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return mixed
     */
    public function execute()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Create Contact Person'));


        return $resultPage;
    }
}
