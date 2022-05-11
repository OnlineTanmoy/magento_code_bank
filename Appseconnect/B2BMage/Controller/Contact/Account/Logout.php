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

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

/**
 * Class Logout
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Logout extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Meta data cookie
     *
     * @var CookieMetadataFactory
     */
    private $_cookieMetadataFactory;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Metadata cookie
     *
     * @var PhpCookieManager
     */
    private $_cookieMetadataManager;
    
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
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSessionFactory;

    /**
     * Logout constructor
     *
     * @param Context                                     $context               context context
     * @param \Magento\Framework\Indexer\IndexerInterface $indexer               indexer
     * @param \Magento\Customer\Model\CustomerFactory     $customerFactory       customer
     * @param \Magento\Catalog\Model\SessionFactory       $catalogSessionFactory catalog session
     * @param Session                                     $customerSession       customer session
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Indexer\IndexerInterface $indexer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\SessionFactory $catalogSessionFactory,
        Session $customerSession
    ) {
    
        $this->indexer = $indexer;
        $this->customerFactory = $customerFactory;
        $this->session = $customerSession;
        $this->catalogSessionFactory = $catalogSessionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     *
     * @return PhpCookieManager
     */
    private function _getCookieManager()
    {
        if (! $this->_cookieMetadataManager) {
            $this->_cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->_cookieMetadataManager;
    }

    /**
     * Retrive catalog session
     *
     * @return PhpCookieManager
     */
    private function _getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\Session::class
        );
        return $this->catalogSession;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     *
     * @return CookieMetadataFactory
     */
    private function _getCookieMetadataFactory()
    {
        if (! $this->_cookieMetadataFactory) {
            $this->_cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->_cookieMetadataFactory;
    }

    /**
     * Customer logout action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $lastCustomerId = $this->session->getId();
        
        $salesrepId = $this->session->getSalesrepId();
        $this->_getCatalogSession()->unsSalesrepId();
        $this->_getCatalogSession()->unsSalesrepMessage();

        $this->session->logout()
            ->setBeforeAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);
        if ($this->_getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->_getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->_getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
        if ($salesrepId) {
            $this->session->unsSalesrepId();
            $this->catalogSessionFactory->create()->setRedirectSalesrepId($salesrepId);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('b2bmage/salesrep/customer_login');
            return $resultRedirect;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/logoutSuccess');
        return $resultRedirect;
    }
}
