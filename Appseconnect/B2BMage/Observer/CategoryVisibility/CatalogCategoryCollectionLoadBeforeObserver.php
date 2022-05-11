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

use Appseconnect\B2BMage\Model\ResourceModel\ContactFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogCategoryCollectionLoadBeforeObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CatalogCategoryCollectionLoadBeforeObserver implements ObserverInterface
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

    /**
     * Class variable Initialize
     *
     * @param Session                                            $session                Session
     * @param ContactFactory                                     $contactResourceFactory ContactResourceFactory
     * @param \Magento\Eav\Model\Entity\Attribute                $eavAttribute           EavAttribute
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig            ScopeConfig
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data    $helperContactPerson    HelperContactPerson
     */
    public function __construct(
        Session $session,
        ContactFactory $contactResourceFactory,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    ) {
        $this->contactResourceFactory = $contactResourceFactory;
        $this->eavAttribute = $eavAttribute;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
    }

    /**
     * Execute
     *
     * @param Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $categoryVisibility = $this->scopeConfig
            ->getValue('insync_category_visibility/select_visibility/select_visibility_type', 'store');
        $contactResourceModel = $this->contactResourceFactory->create();
        $groupId = 0;
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($categoryVisibility == 'group_wise_visibility') {
            if ($customerId) {
                $customerData = $this->helperContactPerson->getCustomerData($customerId);
                $groupId = $customerData["group_id"];
            }
            $attributeId = $this->eavAttribute->getIdByCode('catalog_category', 'customer_group');
            $collection = $observer->getEvent()->getCategoryCollection();
            $entity = $collection->getNewEmptyItem();
            $fileType = constant(get_class($entity) . '::ENTITY');
            if ($fileType == 'catalog_category') {
                $contactResourceModel->getCateogoryList($collection, $groupId, $attributeId);
            }
        }
    }
}
