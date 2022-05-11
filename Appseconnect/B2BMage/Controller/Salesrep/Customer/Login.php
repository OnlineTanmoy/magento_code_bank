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

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Login
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Login extends \Magento\Framework\App\Action\Action
{

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Page
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Cookie meta data manager
     *
     * @var PhpCookieManager
     */
    private $_cookieMetadataManager;

    /**
     * Cookie meta data
     *
     * @var CookieMetadataFactory
     */
    private $_cookieMetadataFactory;

    /**
     * Indexer
     *
     * @var \Magento\Framework\Indexer\IndexerInterface
     */
    public $indexer;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Login constructor
     *
     * @param Context                                         $context                   context
     * @param \Magento\Framework\Indexer\IndexerInterface     $indexer                   indexer
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory           customer
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson       contact person helper
     * @param Magento\Customer\Model\Session                  $customerSession           customer session
     * @param AccountManagementInterface                      $customerAccountManagement customer account manager
     * @param CustomerUrl                                     $customerHelperData        customer helper
     * @param Validator                                       $formKeyValidator          form key
     * @param AccountRedirect                                 $accountRedirect           account redirect
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Indexer\IndexerInterface $indexer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect
    ) {

        parent::__construct(
            $context
        );
        $this->indexer = $indexer;
        $this->customerFactory = $customerFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->session = $customerSession;

    }

    /**
     * Login exiqute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $return = false;
        $customerId = $this->getRequest()->getParam('customer_id');
        $customerId = $customerId;
        $customerData = $this->helperContactPerson->checkCustomerStatus($customerId, true);

        $lastCustomerId = $this->session->getId();
        $redirectSalesrepId = $this->_getCatalogSession()->getRedirectSalesrepId();
        if ($redirectSalesrepId) {
            $this->_getCatalogSession()->unsRedirectSalesrepId();
            $customer = $this->customerFactory->create()->load($redirectSalesrepId);
            $this->session->setCustomerAsLoggedIn($customer);
            if ($this->session->isLoggedIn()) {
                $this->_reindex();
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('b2bmage/salesrep/customer_listing');
                return $resultRedirect;
            }
        } elseif ($customerData && $customerData['customer_type'] == 3 && $customerData['customer_status'] && !$return) {
            $this->session->logout()
                ->setBeforeAuthUrl($this->_redirect->getRefererUrl())
                ->setLastCustomerId($lastCustomerId);
            if ($this->_getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->_getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->_getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }

            $customer = $this->customerFactory->create()->load($customerId);
            $this->session->setCustomerAsLoggedIn($customer);
            $message = '( You have logged in as contact person )';
            $this->session->setSalesrepId($lastCustomerId);
            $this->_getCatalogSession()->setSalesrepMessage($message);
            if ($this->session->isLoggedIn()) {
                $this->_reindex();
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/account');
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('Access Denied.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('');
        return $resultRedirect;
    }

    /**
     * Reindex
     *
     * @return void
     */
    private function _reindex()
    {
        $indexerId = "catalogrule_rule";
        $this->indexer->load($indexerId);
        $this->indexer->reindexAll();
    }

    /**
     * Get Cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function _getCookieManager()
    {
        if (!$this->_cookieMetadataManager) {
            $this->_cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->_cookieMetadataManager;
    }

    /**
     * Get catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    private function _getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->create(\Magento\Catalog\Model\Session::class);
        return $this->catalogSession;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return     CookieMetadataFactory
     * @deprecated
     */
    private function _getCookieMetadataFactory()
    {
        if (!$this->_cookieMetadataFactory) {
            $this->_cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->_cookieMetadataFactory;
    }
}
