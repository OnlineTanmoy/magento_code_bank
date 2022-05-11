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
namespace Appseconnect\B2BMage\Controller\Contact\Account;

use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Class Create
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Create extends \Magento\Customer\Controller\Account\Create
{
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Create constructor
     *
     * @param Context                                            $context           context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig       scope config
     * @param Session                                            $customerSession   customer session
     * @param PageFactory                                        $resultPageFactory result page
     * @param Registration                                       $registration      registration
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        PageFactory $resultPageFactory,
        Registration $registration
    ) {
    
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $customerSession,
            $resultPageFactory,
            $registration
        );
    }

    /**
     * Customer register form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $canRegister = $this->scopeConfig->getValue('insync_account/create/type', 'store');
        if (! $canRegister) {
            $message = __('access denied.');
            $this->messageManager->addError($message);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        
        if ($this->session->isLoggedIn() || ! $this->registration->isAllowed()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
