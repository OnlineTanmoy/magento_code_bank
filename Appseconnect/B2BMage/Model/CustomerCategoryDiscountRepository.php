<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Model;

use Appseconnect\B2BMage\Api\CategoryDiscount\CustomerCategoryDiscountRepositoryInterface;
use Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterfaceFactory;
use Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;
use mysql_xdevapi\DatabaseObject;

/**
 * Class CustomerCategoryDiscountRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerCategoryDiscountRepository implements CustomerCategoryDiscountRepositoryInterface
{
    /**
     * Category discount data
     *
     * @var \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterfaceFactory
     */
    public $categoryDiscountDataFactory;

    /**
     * Category discount
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount\CollectionFactory
     */
    public $categoryDiscountFactory;

    /**
     * Extensible data object converter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * Category collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    public $categoryCollection;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Catalog category discount
     *
     * @var \Appseconnect\B2BMage\Model\CategorydiscountFactory
     */
    public $catalogCategoryDiscountFactory;

    /**
     * CustomerCategoryDiscountRepository constructor.
     *
     * @param CategoryDiscountInterfaceFactory                                $categoryDiscountDataFactory   category discount data
     * @param ResourceModel\Categorydiscount\CollectionFactory                $catDisFactory                 category discount
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter            $extensibleDataObjectConverter extensible data object converter
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection            category collection
     * @param \Magento\Customer\Model\CustomerFactory                         $customerFactory               customer
     * @param CategorydiscountFactory                                         $catalogCategoryDiscount       catalog category
     */
    public function __construct(
        CategoryDiscountInterfaceFactory $categoryDiscountDataFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount\CollectionFactory $catDisFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\CategorydiscountFactory $catalogCategoryDiscount
    ) {

        $this->categoryDiscountDataFactory = $categoryDiscountDataFactory;
        $this->categoryDiscountFactory = $catDisFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->categoryCollection = $categoryCollection;
        $this->customerFactory = $customerFactory;
        $this->catalogCategoryDiscountFactory = $catalogCategoryDiscount;
    }

    /**
     * Data validator
     *
     * @param mixed $data data
     *
     * @return string[]
     */
    public function dataValidator($data)
    {
        $error = [];
        // Required Fields
        $requiredFields[] = 'category_id';
        $requiredFields[] = 'discount_factor';
        $requiredFields[] = 'is_active';
        $requiredFields[] = 'discount_type';
        foreach ($requiredFields as $requiredValues) {
            if (!isset($data[$requiredValues])) {
                $error[] = "Field [" . $requiredValues . "] is required";
            } else {
                if ($requiredValues == 'category_id') {
                    $categoryIds = [];
                    $categoryFactory = $this->categoryCollection->create();
                    $categories = $categoryFactory->addAttributeToSelect('*');
                    $categoryValues = $categories->getData();
                    foreach ($categoryValues as $categoryData) {
                        $categoryIds[] = $categoryData['entity_id'];
                    }
                    if (!in_array($data['category_id'], $categoryIds)) {
                        $error[] = "Category Id[" . $data['category_id'] . "] do not exist";
                    }
                } elseif ($requiredValues == 'discount_factor') {
                    if ($data['discount_factor'] < 0 || $data['discount_factor'] > 100) {
                        $error[] = "Discount Factor can not be negative or more than 100.";
                    }
                } elseif ($requiredValues == 'is_active') {
                    if ($data['is_active'] != 0 && $data['is_active'] != 1) {
                        $error[] = "Status should have binary values.";
                    }
                } elseif ($requiredValues == 'discount_type') {
                    if ($data['discount_type'] != 0 && $data['discount_type'] != 1) {
                        $error[] = "Discount Type should have binary values.";
                    }
                }
            }
        }
        return $error;
    }

    /**
     * Create customer category discount
     *
     * @param CategoryDiscountInterface $categoryDiscount category discount
     *
     * @return CategoryDiscountInterface|string[]|\string[][]
     */
    public function createCustomerCategoryDiscount(CategoryDiscountInterface $categoryDiscount)
    {
        $categoryDiscountFactory = $this->categoryDiscountFactory->create();
        $categoryDiscountDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray(
                $categoryDiscount,
                [],
                'Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface'
            );
        if (!isset($categoryDiscountDataArray['customer_id'])) {
            throw new \Magento\Framework\Exception\InputException(
                __("[customer_id] is a required field")
            );
        }
        $customerId = $categoryDiscountDataArray['customer_id'];
        if (!($this->customerFactory->create()->load($customerId)->getEntityId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer id doesn't exist", $customerId)
            );
        }
        $categoryDiscountData = $categoryDiscountDataArray['categorydiscount_data'];
        $categoryDiscountModel = $this->catalogCategoryDiscountFactory->create();
        $return = [];
        if (count($categoryDiscountData) == 1) {
            $result = $this->saveCategoryDiscount($categoryDiscountData, $customerId, $categoryDiscountModel, 1);
        } else {
            $result = $this->saveCategoryDiscount($categoryDiscountData, $customerId, $categoryDiscountModel, 0);
        }
        return $result;
    }

    /**
     * Save
     *
     * @param $categoryDiscountData  category discount
     * @param $customerId            customer id
     * @param $categoryDiscountModel category discount model
     * @param $flag                  flag
     *
     * @return array
     */
    public function saveCategoryDiscount($categoryDiscountData, $customerId, $categoryDiscountModel, $flag)
    {
        foreach ($categoryDiscountData as $data) {
            $returnData = [];
            $errorCheck = '';
            $errorCheck = $this->dataValidator($data);
            if (!empty($errorCheck)) {
                if ($flag == 1) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Required fields error", $errorCheck)
                    );
                } else {
                    $returnData['error'] = $errorCheck;
                }
            } else {
                $data['customer_id'] = $customerId;
                if (!isset($data['categorydiscount_id'])) {
                    $existData = $this->categoryDiscountFactory->create()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('category_id', $data['category_id'])
                        ->getData() ? true : false;
                    if ($existData) {
                        if ($flag == 1) {
                            throw new \Magento\Framework\Exception\CouldNotSaveException(
                                __("Duplicate Entry for Category Id", $data['category_id'])
                            );
                        } else {
                            $returnData['error'] = "Duplicate Entry for Category Id[" . $data['category_id'] . "]";
                        }
                    } else {
                        $returnData = $this->categoryDiscountModelSave($categoryDiscountModel, $data);
                    }
                } else {
                    if ($flag == 1) {
                        throw new \Magento\Framework\Exception\CouldNotSaveException(
                            __("Invalid data for Category Discount Id", $data['categorydiscount_id'])
                        );
                    } else {
                        $returnData['error'] = "Invalid data for ID [" . $data['categorydiscount_id'] . "]";
                    }
                }
            }
            $return[] = $returnData;
        }
        return $return;
    }

    /**
     * Category discount model save
     *
     * @param $categoryDiscountModel category discount model
     * @param $data                  data
     *
     * @return mixed
     */
    public function categoryDiscountModelSave($categoryDiscountModel, $data)
    {
        $categoryDiscountModel->setData($data);
        $categoryDiscountModel->save();
        return $this->catalogCategoryDiscountFactory->create()
            ->load($categoryDiscountModel->getId())
            ->getData();
    }

    /**
     * Get customer category discount
     *
     * @param int $customerId customer id
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface
     */
    public function getCustomerCategoryDiscount($customerId)
    {
        $categoryDiscountDataFactory = $this->categoryDiscountDataFactory->create();
        $existCustomerId = $this->customerFactory->create()
            ->load($customerId)
            ->getId() ? true : false;
        if ($existCustomerId == false) {
            throw NoSuchEntityException::singleField('customerId', $customerId);
        } else {
            $categoryDiscountModel = $this->categoryDiscountFactory
                ->create()
                ->addFieldToFilter('customer_id', $customerId);
            if ($categoryDiscountModel->getData()) {
                return $categoryDiscountModel->getData();
            } else {
                throw new InputException(
                    __("Category discount doesn't exist for the given customer id", $customerId)
                );
            }
        }
    }

    /**
     * Update
     *
     * @param CategoryDiscountInterface $categoryDiscount category discount
     *
     * @return array
     */
    public function updateCustomerCategoryDiscount(CategoryDiscountInterface $categoryDiscount)
    {
        $categoryDiscountFactory = $this->categoryDiscountFactory->create();
        $categoryDiscountDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray(
                $categoryDiscount,
                [],
                'Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface'
            );
        $customerId = $categoryDiscount->getCustomerId();
        if (!($this->customerFactory->create()->load($customerId)->getEntityId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer id doesn't exist", $customerId)
            );
        }
        $categoryDiscountData = $categoryDiscountDataArray['categorydiscount_data'];
        $categoryDiscountModel = $this->catalogCategoryDiscountFactory->create();
        $return = [];
        foreach ($categoryDiscountData as $data) {
            $returnData = [];
            $errorCheck = '';
            $errorCheck = $this->dataValidator($data);
            if (!empty($errorCheck)) {
                $returnData['error'] = $errorCheck;
            } else {
                $categoryDiscountModel = $this->catalogCategoryDiscountFactory->create();
                $categoryDiscountFactory = $this->categoryDiscountFactory->create()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('category_id', $data['category_id']);
                if ($categoryDiscountFactory->getData()) {
                    $data ['categorydiscount_id'] = $this->getCategoryDiscountId($customerId, $data);
                    $this->categoryDiscountSave($categoryDiscountModel, $data);
                } else {
                    $data['customer_id'] = $customerId;
                    $this->categoryDiscountSave($categoryDiscountModel, $data);
                }
                $returnData = $this->getCategoryDiscountData($categoryDiscountModel);
            }
            $return[] = $returnData;
        }
        return $return;
    }

    /**
     * Category discount
     *
     * @param $categoryDiscountModel category discount model
     * @param $data                  data
     *
     * @return void
     */
    public function categoryDiscountSave($categoryDiscountModel, $data)
    {
        $categoryDiscountModel->setData($data);
        $categoryDiscountModel->save();
    }

    /**
     * Get category discount id
     *
     * @param int   $customerId customer id
     * @param mixed $data       data
     *
     * @return mixed
     */
    public function getCategoryDiscountId($customerId, $data)
    {
        $categoryDiscountData = $this->categoryDiscountFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('category_id', $data['category_id'])->load()->getData();
        $categoryDiscountId = $categoryDiscountData[0];
        return $categoryDiscountId['categorydiscount_id'];
    }

    /**
     * Get category discount data
     *
     * @param $categoryDiscountModel category discount model
     *
     * @return mixed
     */
    public function getCategoryDiscountData($categoryDiscountModel)
    {
        return $this->catalogCategoryDiscountFactory->create()
            ->load($categoryDiscountModel->getId())
            ->getData();
    }
}
