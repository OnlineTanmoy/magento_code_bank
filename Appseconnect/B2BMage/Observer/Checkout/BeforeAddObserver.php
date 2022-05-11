<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\Checkout;

use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeAddObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class BeforeAddObserver implements ObserverInterface
{

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * ScopeConfigInterface
     *
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * UrlInterface
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $url;

    /**
     * BeforeAddObserver constructor.
     *
     * @param Session                                            $session         Session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig     ScopeConfig
     * @param \Magento\Framework\App\ResponseFactory             $responseFactory ResponseFactory
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager  MessageManager
     * @param \Magento\Framework\UrlInterface                    $url             Url
     */
    public function __construct(
        Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->customerSession = $session;
        $this->scopeConfig = $scopeConfig;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->url = $url;
    }

    /**
     * $observer
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $guestCartDisabled = $this->scopeConfig->getValue(
            'insync_category_visibility/select_checkout_visibility/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($guestCartDisabled) {
            if (!$this->customerSession->isLoggedIn()) {
                $this->messageManager->addError('Before add to cart You need to login first');
                $redirectionUrl = $this->url->getUrl('customer/account/login');
                $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
            }
        }

        return $this;
    }
}
