<?php
namespace Appseconnect\DisableCategoryProduct\Observer\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

class CatalogCategoryCollectionObserver implements ObserverInterface
{
    /**
     *
     * @var Session
     */
    public $session;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    protected $httpContext;

    /**
     * @param Session $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->customerSession = $session;
        $this->scopeConfig = $scopeConfig;
        $this->httpContext = $httpContext;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $catalogVisibility = $this->scopeConfig
            ->getValue('catalog_product_visibility/general/enable_catalog_product_visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $customerId = $this->httpContext->getValue('customer_id');

        $categoryCollection = $observer->getEvent()->getCategoryCollection();

        if($catalogVisibility == 1)
        {
            if (!$customerId) {
                $categoryCollection = $categoryCollection
                    ->addAttributeToFilter('entity_id', array('null' => true));
                return $categoryCollection;
            }
        }
    }
}
