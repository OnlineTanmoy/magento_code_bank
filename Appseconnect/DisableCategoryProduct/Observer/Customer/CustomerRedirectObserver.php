<?php
namespace Appseconnect\DisableCategoryProduct\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

class CustomerRedirectObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    protected $httpContext;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    public $request;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Request\Http $request
     * @param Session $session
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $request,
        Session $session
    ) {
        $this->redirect = $redirect;
        $this->httpContext = $httpContext;
        $this->scopeConfig = $scopeConfig;
        $this->_urlInterface = $urlInterface;
        $this->request = $request;
        $this->customerSession = $session;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $catalogVisibility = $this->scopeConfig
            ->getValue('catalog_product_visibility/general/enable_catalog_product_visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if($catalogVisibility)
        {
            if ($this->request->getFullActionName() == 'catalog_category_view') {
                if (!$this->customerSession->isLoggedIn()) {
                    $url = $this->_urlInterface->getUrl('customer/account/login');
                    if(strpos($this->request->getPathInfo(), '/customer/account/') !== 0)
                    {
                        # redirect to /customer/account/login
                        $observer->getControllerAction()
                                 ->getResponse()
                                 ->setRedirect($url);
                    }
                }
            }
            if ($this->request->getFullActionName() == 'catalog_product_view') {
                if (!$this->customerSession->isLoggedIn()) {
                    $url = $this->_urlInterface->getUrl('customer/account/login');
                    if(strpos($this->request->getPathInfo(), '/customer/account/') !== 0)
                    {
                        # redirect to /customer/account/login
                        $observer->getControllerAction()
                                 ->getResponse()
                                 ->setRedirect($url);
                    }
                }
            }
        }
    }
}
