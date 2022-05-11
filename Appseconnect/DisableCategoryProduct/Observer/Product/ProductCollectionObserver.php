<?php
namespace Appseconnect\DisableCategoryProduct\Observer\Product;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

class ProductCollectionObserver implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    protected $httpContext;

    /**
     * @param Session $session
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $session;
        $this->scopeConfig = $scopeConfig;
        $this->httpContext = $httpContext;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $catalogVisibility = $this->scopeConfig
            ->getValue('catalog_product_visibility/general/enable_catalog_product_visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $customerId = $this->httpContext->getValue('customer_id');
        $categoryProductCollection = $observer->getEvent()->getCollection();

        if($catalogVisibility == 1)
        {
            if (!$customerId) {
                foreach($categoryProductCollection as $data){
                    $categoryProductCollection->removeItemByKey($data->getEntityId());
                }
                return $categoryProductCollection;
            }
        }
    }
}
