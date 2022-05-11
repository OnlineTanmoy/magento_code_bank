<?php

/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\CategoryDiscount;

use Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $categoryDiscountCollectionFactory;
    
    /**
     * CategoryCollectionFactory
     *
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Data constructor.
     *
     * @param CollectionFactory         $categoryDiscountCollectionFactory CategoryDiscountCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory         CategoryCollectionFactory
     */
    public function __construct(
        CollectionFactory $categoryDiscountCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryDiscountCollectionFactory = $categoryDiscountCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * GetCategoryDiscountAmount
     *
     * @param float $finalprice  Finalprice
     * @param int   $customerId  CustomerId
     * @param int   $categoryids Categoryids
     *
     * @return float|NULL
     */
    public function getCategoryDiscountAmount($finalprice, $customerId, $categoryids)
    {
        $categoryCollection = $this->categoryDiscountCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter(
                'category_id', [
                "in" => $categoryids
                ]
            )
            ->setOrder('discount_factor', "DESC")
            ->setPageSize(1)
            ->setCurPage(1);
        $output = $categoryCollection->getData();
        if (is_array($output) && ! empty($output)) {
            foreach ($output as $data) {
                if ($data['is_active'] == 1) {
                    if ($data['discount_type'] == 1)
                    {
                        $discountedPrice = $finalprice * (1 - $data['discount_factor'] / 100);
                    }
                    else
                    {
                        $discountedPrice = $finalprice -$data['discount_factor'];
                    }
                } else {
                    $discountedPrice = '';
                }
            }
        } else {
            $discountedPrice = '';
        }
        return $discountedPrice;
    }

    /**
     * GetCategoryList
     *
     * @param int $customerId CustomerId
     *
     * @return array
     */
    public function getCategoryList($customerId = null)
    {
        $categoryData = [];
        if ($customerId) {
            $categoryValues = $this->categoryDiscountCollectionFactory->create()
                ->addFieldToSelect('category_id')
                ->addFieldToFilter('customer_id', $customerId);
            $categoryData = [];
            foreach ($categoryValues as $data) {
                $categoryData[] = $data['category_id'];
            }
        }
        $categories = $this->categoryCollectionFactory->create()->addAttributeToSelect('*');
        if ($customerId) {
            $categories->addAttributeToFilter(
                'entity_id', [
                'nin' => $categoryData
                ]
            );
        }
        $result = [];
        foreach ($categories as $category) {
            if ($category->getId()) {
                $result[$category->getId()] = $category->getName();
            }
        }
        return $result;
    }
}
