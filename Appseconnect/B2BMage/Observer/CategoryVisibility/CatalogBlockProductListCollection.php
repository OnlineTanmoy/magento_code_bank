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
namespace Appseconnect\B2BMage\Observer\CategoryVisibility;

use Magento\Framework\Event\Observer;
use Appseconnect\B2BMage\Model\ResourceModel\ContactFactory;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

/**
 * Class CatalogBlockProductListCollection
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CatalogBlockProductListCollection implements ObserverInterface
{

    /**
     * Session
     *
     * @var Session
     */
    public $session;
    
    /**
     * ContactFactory
     *
     * @var ContactFactory
     */
    public $contactResourceFactory;
    
    /**
     * Attribute
     *
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    public $eavAttribute;
    
    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    public $httpContext;

    /**
     * Class variable Initialize
     *
     * @param Session                                            $session                Session
     * @param ContactFactory                                     $contactResourceFactory ContactResourceFactory
     * @param \Magento\Eav\Model\Entity\Attribute                $eavAttribute           EavAttribute
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig            ScopeConfig
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data    $helperContactPerson    HelperContactPerson
     * @param \Magento\Framework\App\Http\Context                $httpContext
     */
    public function __construct(
        Session $session,
        ContactFactory $contactResourceFactory,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->contactResourceFactory = $contactResourceFactory;
        $this->eavAttribute = $eavAttribute;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->httpContext = $httpContext;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $contactResourceModel = $this->contactResourceFactory->create();
        $customerId = $this->httpContext->getValue('customer_id');
        $categoryVisibility = $this->scopeConfig
            ->getValue('insync_category_visibility/select_visibility/select_visibility_type', 'store');
        $productVisibility = $this->scopeConfig
            ->getValue('insync_category_visibility/select_product_visibility/active', 'store');
        
        if ($customerId && $categoryVisibility == 'group_wise_visibility') {
            $customerData = $this->helperContactPerson->getCustomerData($customerId);
            
            $customerType = $customerData["customer_type"];
            $groupId = $customerData["group_id"];
            if ($customerType == 3) {
                $attributeId = $this->eavAttribute->getIdByCode('catalog_category', 'customer_group');
                $collection = $contactResourceModel->getProductList($collection, $groupId, $attributeId);
            }
        } elseif (! $customerId && ! $productVisibility) {
            $collection->addAttributeToFilter('entity_id', 0);
        }
    }
}
