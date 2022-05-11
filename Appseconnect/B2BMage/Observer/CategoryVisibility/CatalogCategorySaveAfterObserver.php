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
class CatalogCategorySaveAfterObserver implements ObserverInterface
{

    /**
     * CategoryFactory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * CatalogCategorySaveAfterObserver constructor.
     *
     * @param \Magento\Catalog\Model\CategoryFactory             $categoryFactory CategoryFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig     ScopeConfig
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->scopeConfig = $scopeConfig;
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
        if ($categoryVisibility == 'group_wise_visibility') {
            $category = $observer->getEvent()->getCategory();

            $customerGroup = $category->getData('customer_group');
            $childrenCount = $category->getData('children_count');
            if ($childrenCount > 0) {
                $categoryCollection = $this->categoryFactory->create()->load($category->getData('entity_id'));
                $childCategoryId = $categoryCollection->getResource()->getChildren($categoryCollection, true);
                $customerGroup = (!empty($customerGroup)) ? implode(',', $customerGroup) : "";
                if (!empty($childCategoryId)) {
                    foreach ($childCategoryId as $categoryId) {
                        $categoryModel = $this->loadCategory($categoryId);
                        $categoryModel->setData('customer_group', '' . $customerGroup . '');
                        $this->categoryFactory->create()
                            ->getResource()
                            ->saveAttribute($categoryModel, 'customer_group');
                    }
                }
            }
        }
    }

    /**
     * LoadCategory
     *
     * @param int $categoryId CategoryId
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function loadCategory($categoryId)
    {
        $model = $this->categoryFactory->create()->load($categoryId);
        return $model;
    }
}
