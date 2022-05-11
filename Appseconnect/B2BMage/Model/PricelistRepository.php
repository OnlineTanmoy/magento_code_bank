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

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\ResourceModel\PricelistProductFactory as PricelistProductResourceFactory;
use Appseconnect\B2BMage\Api\Pricelist\PricelistRepositoryInterface;

/**
 * Class PricelistRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class PricelistRepository implements PricelistRepositoryInterface
{
    /**
     * Pricelist product resource
     *
     * @var PricelistProductResourceFactory
     */
    public $pricelistProductResourceFactory;
    
    /**
     * Resource
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;
    
    /**
     * Customer resource
     *
     * @var \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    public $customerResourceFactory;
    
    /**
     * Pricelist price
     *
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPriceFactory;
    
    /**
     * Price list price collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistPriceCollectionFactory;
    
    /**
     * Price list product collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\PricelistProduct\CollectionFactory
     */
    public $pricelistProductCollectionFactory;
    
    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Extensible data object
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;
    
    /**
     * Price list model
     *
     * @var \Appseconnect\B2BMage\Model\Price
     */
    public $pricelistModel;
    
    /**
     * Price list product model
     *
     * @var \Appseconnect\B2BMage\Model\PricelistProduct
     */
    public $pricelistProductModel;
    
    /**
     * Price list helper
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;
    
    /**
     * Website collection
     *
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollection;

    public $pricelistProductFactory;

    /**
     * PricelistRepository constructor.
     *
     * @param PricelistProductResourceFactory                              $pricelistProductResourceFactory Pricelist product resource
     * @param \Magento\Framework\App\ResourceConnection                    $resources                       resource
     * @param ResourceModel\Price\CollectionFactory                        $pricelistPriceCollectionFactory pricelist price collection
     * @param ResourceModel\PricelistProduct\CollectionFactory             $pricelistProductCollection      price list product collection
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory        $customerResourceFactory         customer resource
     * @param PriceFactory                                                 $pricelistPriceFactory           price list price
     * @param \Magento\Catalog\Model\ProductFactory                        $productFactory                  product
     * @param \Magento\Customer\Model\CustomerFactory                      $customerFactory                 customer
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter         $extensibleDataObjectConverter   extensible data object converter
     * @param Price                                                        $pricelistModel                  price list
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data                  $helperPricelist                 pricelist helper
     * @param PricelistProduct                                             $pricelistProductModel           price list product model
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollection               website collection
     * @param \Appseconnect\B2BMage\Model\PricelistProductFactory          $pricelistProductFactory         pricelistProductFactory
     */
    public function __construct(
        PricelistProductResourceFactory $pricelistProductResourceFactory,
        \Magento\Framework\App\ResourceConnection $resources,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\PricelistProduct\CollectionFactory $pricelistProductCollection,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Appseconnect\B2BMage\Model\PriceFactory $pricelistPriceFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Appseconnect\B2BMage\Model\Price $pricelistModel,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Model\PricelistProduct $pricelistProductModel,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollection,
        \Appseconnect\B2BMage\Model\PricelistProductFactory $pricelistProductFactory
    ) {
        $this->pricelistProductResourceFactory = $pricelistProductResourceFactory;
        $this->resources = $resources;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->pricelistPriceFactory = $pricelistPriceFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->pricelistProductCollectionFactory = $pricelistProductCollection;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->pricelistModel = $pricelistModel;
        $this->pricelistProductModel = $pricelistProductModel;
        $this->helperPricelist = $helperPricelist;
        $this->websiteCollection = $websiteCollection;
        $this->pricelistProductFactory = $pricelistProductFactory;
    }

    /**
     * Check require field
     *
     * @param $requiredFields required fields
     * @param $pricelistValue price list value
     *
     * @return void
     */
    public function checkRequiredFields($requiredFields, $pricelistValue)
    {
        foreach ($requiredFields as $requiredValues) {
            if (isset($pricelistValue[$requiredValues]) && trim($pricelistValue[$requiredValues]) == '') {
                throw new \Magento\Framework\Exception\InputException(
                    __('The [' . $requiredValues . '] can not be empty.')
                );
            }
            if (! isset($pricelistValue[$requiredValues])) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Field [' . $requiredValues . '] is required.')
                );
            }
        }
    }

    /**
     * Get wesite
     *
     * @return array
     */
    public function getWebsite()
    {
        $customer = $this->websiteCollection->create();
        $output = $customer->getData();
        $result =  [];
        foreach ($output as $val) {
            $result [$val ['website_id']] =$val ['name'];
        }
        return $result;
    }

    /**
     * Is website is exists
     *
     * @param array $pricelistValue pricelist value
     *
     * @throws \Magento\Framework\Exception\InputException
     * @return void
     */
    public function isWebsiteIdExist($pricelistValue)
    {
        $websiteIds = $this->getWebsite();
        foreach ($websiteIds as $key => $websiteValues) {
            $checkWebsiteId = false;
            if (! array_key_exists($pricelistValue['website_id'], $websiteIds)) {
                $checkWebsiteId = true;
            }
            break;
        }
        if ($checkWebsiteId) {
            throw new \Magento\Framework\Exception\InputException(
                __('Website ID [' . $pricelistValue['website_id'] . '] does not exist.')
            );
        }
    }

    /**
     * Is price list valid
     *
     * @param array $pricelistValue price list value
     *
     * @throws \Magento\Framework\Exception\InputException
     * @return void
     */
    public function isPricelistValid($pricelistValue)
    {
        if (array_key_exists('id', $pricelistValue) && ! empty($pricelistValue['id'])) {
            $pricelistModel = $this->pricelistModel->load($pricelistValue['id']);
            
            if (! $pricelistModel->getId()) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Pricelist with ID [' . $pricelistValue['id'] . '] does not exist.')
                );
            }
        }
    }
   
    /**
     * Pricelust data validator
     *
     * @param array $pricelistValue pricelist value
     *
     * @throws \Magento\Framework\Exception\InputException
     * @return void
     */
    public function pricelistDataValidator($pricelistValue)
    {
        // Required Fields
        $requiredFields[] = 'website_id';
        $requiredFields[] = 'pricelist_name';
        $requiredFields[] = 'factor';
        $requiredFields[] = 'is_active';
        foreach ($requiredFields as $requiredValues) {
            if ($requiredValues == 'website_id') {
                $this->isWebsiteIdExist($pricelistValue);
            } elseif ($requiredValues == 'factor') {
                if ($pricelistValue['factor'] < 0 || $pricelistValue['factor'] > 100) {
                    throw new \Magento\Framework\Exception\InputException(
                        __('Factor can not be negative or more than 100.')
                    );
                }
            } elseif ($requiredValues == 'is_active') {
                if ($pricelistValue['is_active'] != 0 && $pricelistValue['is_active'] != 1) {
                    throw new \Magento\Framework\Exception\InputException(
                        __('Status should have binary values.')
                    );
                }
            }
        }
    }
    
    /**
     * Load pricelist
     *
     * @param int $pricelistParentId pricelist parent id
     *
     * @return mixed
     */
    public function loadPricelist($pricelistParentId)
    {
        return $this->pricelistModel->load($pricelistParentId);
    }

    /**
     * Load entity
     *
     * @param int $customerId customer id
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($customerId)
    {
        $result = [];
        $error = [];
        $connection = $this->resources->getConnection();
        $customerCollection = $this->customerFactory->create()->load($customerId);
        if (! $customerId) {
            throw new \Magento\Framework\Exception\InputException(__('Customer ID required'));
        } elseif (! $customerCollection->getId()) {
            throw new \Magento\Framework\Exception\InputException(
                __('Customer ID [' . $customerId . '] does not exist')
            );
        } elseif (! $customerCollection->getData('pricelist_code')) {
            throw new \Magento\Framework\Exception\InputException(
                __('Customer ID [' . $customerId . '] not assigned to any pricelist')
            );
        }
        $pricelistId = $customerCollection->getData('pricelist_code');
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()->addFieldToFilter('id', $pricelistId);
        $productCollection = $this->pricelistProductCollectionFactory
            ->create()->addFieldToFilter('pricelist_id', $pricelistId);
        $pricelistData = [];
        $pricelistData = $pricelistCollection->getData();
        $productData = $productCollection->getData();
        $assignedProductData = [];
        foreach ($productData as $productDataValue) {
            $productId = $this->getItemId($productDataValue['sku']);
            $pricelistPrice = '';
            if ($productDataValue['original_price']) {
                $pricelistPrice = $this->helperPricelist
                    ->getAmount($productId, $productDataValue['original_price'], $pricelistId);
            }
            $assignedProductData['sku'] = $productDataValue['sku'];
            $assignedProductData['pricelist_price'] = $pricelistPrice;
            $pricelistData[0]['products'][] = $assignedProductData;
        }
        $finalData = $pricelistData;
        return $finalData;
    }
    
    /**
     * Get item id
     *
     * @param string $productSku product sku
     *
     * @return int
     */
    public function getItemId($productSku)
    {
        $itemId = $this->productFactory->create()->getIdBySku($productSku);
        return $itemId;
    }

    /**
     * Update Pricelist.
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[] $pricelist pricelist
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update(\Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface $pricelist)
    {
        $result = [];
        $error = [];
        $pricelistId = $pricelist->getId();
        $pricelistCollection = $this->pricelistModel->load($pricelistId);
        
        if (! $pricelistCollection->getId()) {
            throw new \Magento\Framework\Exception\InputException(
                __('Pricelist with ID [' . $pricelistId . '] does not exist.')
            );
        }
        $pricelistData = $this->extensibleDataObjectConverter
            ->toNestedArray($pricelist, [], '\Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface');
        
        // Data Validation for single pricelist update (only)
        $errorCheck = '';
        $errorCheck = $this->pricelistDataValidator($pricelistData);
        
        $pricelistData['id'] = $pricelistId;
        $pricelistLastInsertId = '';
        if (empty($errorCheck)) {
            $pricelistData['discount_factor'] = $pricelistData['factor'];
            $this->pricelistModel->setData($pricelistData)->save();
            $pricelistLastInsertId = $this->pricelistModel->getId();
            $pricelist->setId($pricelistLastInsertId);
        }

        $pricelistProduct = $this->pricelistProductFactory->create()->getCollection()
            ->addFieldToFilter('pricelist_id', $pricelistId);

        foreach ($pricelistProduct as $singlePricelistProduct) {

            $productId = $singlePricelistProduct['product_id'];
            $isManual = $singlePricelistProduct['is_manual'];

            $product = $this->productFactory->create()->load($productId);
            $finalPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue() * $pricelistData['discount_factor'];
            $originalPrice = $finalPrice;

            if (!$isManual) {
                $singlePricelistProduct->setData('original_price', $originalPrice);
                $singlePricelistProduct->setData('final_price', $finalPrice);
                $singlePricelistProduct->save();
            }
        }

        return $pricelist;
    }

    /**
     * Performs assign operation of pricelist to a customer.
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface $entity entity
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface
     */
    public function assignPricelist(\Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface $entity)
    {
        $customer = $this->customerFactory->create();
        $customerId = $entity->getCustomerId();
        $pricelistId = $entity->getPricelistId();
        if (! ($customer->load($customerId)->getEntityId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer Id doesn't exist", $customerId)
            );
        } elseif (! ($this->pricelistPriceFactory->create()        ->load($pricelistId)        ->getId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Pricelist Id doesn't exist", $pricelistId)
            );
        }
        $customerData = $customer->getDataModel();
        $customer->setId($customerId);
        $customerData->setCustomAttribute('pricelist_code', $pricelistId);
        $customer->updateData($customerData);
        $customerResource = $this->customerResourceFactory->create();
        $customerResource->saveAttribute($customer, 'pricelist_code');
        return $entity;
    }

    /**
     * Create Pricelist.
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[] $pricelist pricelist
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(\Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface $pricelist)
    {
        $pricelistName = $pricelist->getPricelistName();
        $isPricelistExists = $this->pricelistModel->getCollection()
            ->addFieldToFilter('pricelist_name', $pricelistName)
            ->getData() ? 1 : 0;
        
        if ($isPricelistExists) {
            throw new \Magento\Framework\Exception\InputException(
                __('Pricelist : ' . $pricelistName . ' already exists.')
            );
        }
        
        $pricelistData = $this->extensibleDataObjectConverter
            ->toNestedArray($pricelist, [], '\Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface');
        
        // Data Validation for single pricelist upload (only)
        $errorCheck = '';
        $errorCheck = $this->pricelistDataValidator($pricelistData);
        
        $pricelistId = '';
        if (empty($errorCheck)) {
            $pricelistData['discount_factor'] = $pricelistData['factor'];
            $this->pricelistModel->setData($pricelistData)->save();
            $pricelistId = $this->pricelistModel->getId();
            $pricelistData = $this->pricelistModel->load($pricelistId)->getData();
            $pricelist->setId($pricelistId);
        }
        return $pricelist;
    }

    /**
     * Product data validator
     *
     * @param array $products products
     *
     * @throws \Magento\Framework\Exception\InputException
     * @return void
     */
    private function _productDataValidator($products)
    {
        $flag = 0;
        $requiredFields[] = 'sku';
        $requiredFields[] = 'price';
        $requiredFields[] = 'is_manual';
        foreach ($requiredFields as $requiredValues) {
            if (isset($products[$requiredValues]) && trim($products[$requiredValues]) == '') {
                throw new \Magento\Framework\Exception\InputException(
                    __('The [' . $requiredValues . '] can not be empty.')
                );
            }
            if (! isset($products[$requiredValues])) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Field [' . $requiredValues . '] is required.')
                );
            } else {
                if ($requiredValues == 'sku') {
                    $productId = $this->getItemId($products['sku']);
                    if (! $productId) {
                        $flag = 1;
                    }
                } elseif ($requiredValues == 'price') {
                    if ($products['price'] < 0) {
                        $flag = 1;
                    }
                } elseif ($requiredValues == 'is_manual') {
                    if (isset($products['is_manial']) && $products['is_manial'] == '') {
                        $flag = 1;
                    }
                }
            }
        }
    }

    /**
     * Assign products
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface $product product
     * @param false                                                           $isAdmin isadmin
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface
     */
    public function assignProducts(
        \Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface $product,
        $isAdmin = false
    ) {
        $pricelistId = $product->getPricelistId();
        if (! $this->pricelistModel->load($pricelistId)->getData()) {
            throw new \Magento\Framework\Exception\InputException(
                __('Invalid Pricelist Id : ' . $pricelistId . '.')
            );
        }
        $products = $this->extensibleDataObjectConverter
            ->toNestedArray($product, [], '\Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface');
        $isErrorExists = 0;
        $duplicateSkus = [];
        foreach ($products['product_data'] as $productData) {
            if (array_key_exists($productData['sku'], $duplicateSkus)) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Duplicate sku exists.')
                );
            }
            $duplicateSkus[$productData['sku']] = $productData['price'];
            $isErrorExists = $this->_productDataValidator($productData);
            if ($isErrorExists) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Invalid data provided in the "product_data" section')
                );
            }
        }
        if ($isAdmin) {
            $this->pricelistProductResourceFactory->create()->removeMapping($pricelistId);
        }
        
        foreach ($products['product_data'] as $postData) {
            $existingData = $this->getPricelistProductCollection($pricelistId, $postData['sku']);
            $postData['product_pricelist_map_id'] = null;
            if ($existingData) {
                $postData['product_pricelist_map_id'] = $existingData['product_pricelist_map_id'];
            }
            $postData['product_id'] = $this->getItemId($postData['sku']);
            $postData['pricelist_id'] = $pricelistId;
            $postData['original_price'] = $postData['price'];
            $postData['final_price'] = $postData['price'];
            $this->pricelistProductModelSave($postData);
        }
        return $product;
    }
    /**
     * Get pricelist product collection
     *
     * @param int    $pricelistId pricelist id
     * @param string $sku         sku
     *
     * @return NULL|mixed
     */
    public function getPricelistProductCollection($pricelistId, $sku)
    {
        $existingData = null;
        $prevData = $this->pricelistProductModel->getCollection()
            ->addFieldToFilter('pricelist_id', $pricelistId)
            ->addFieldToFilter('sku', $sku)->load()->getData();
        if ($prevData) {
            $existingData = $prevData[0];
        }
        return $existingData;
    }
    /**
     * Price list product model save
     *
     * @param mixed $postData postdata
     *
     * @return void
     */
    public function pricelistProductModelSave($postData)
    {
        $this->pricelistProductModel->setData($postData)->save();
    }
}
