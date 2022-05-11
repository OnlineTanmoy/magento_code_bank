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

use Appseconnect\B2BMage\Api\CustomerSpecialPrice\SpecialPriceRepositoryInterface;
use Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class SpecialPriceRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SpecialPriceRepository implements SpecialPriceRepositoryInterface
{
    
    /**
     * Special price resource
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\SpecialpriceFactory
     */
    public $specialPriceResourceFactory;
    
    /**
     * Special price map
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory
     */
    public $specialPriceMapFactory;
    
    /**
     * Customer
     *
     * @var \Appseconnect\B2BMage\Model\CustomerFactory
     */
    public $specialPriceCustomerFactory;
    
    /**
     * Pricelist price
     *
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPriceFactory;
    
    /**
     * Special price helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data
     */
    public $helperSpecialPrice;
    
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
     * Product Repository
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    
    /**
     * Special price map
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory
     */
    public $specialPriceMapCollectionFactory;
    
    /**
     * Special price collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory
     */
    public $specialPriceCollection;
    
    /**
     * Extensible data object converter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * SpecialPriceRepository constructor.
     *
     * @param ResourceModel\SpecialpriceFactory                      $specialPriceResourceFactory   special price resource
     * @param ResourceModel\Specialprice\CollectionFactory           $specialPriceMap               special price map
     * @param PriceFactory                                           $pricelistPriceFactory         pricelist price
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperSpecialPrice            special price
     * @param CustomerFactory                                        $specialPriceCustomerFactory   special price customer
     * @param \Magento\Customer\Model\CustomerFactory                $customerFactory               customer
     * @param \Magento\Catalog\Model\ProductFactory                  $productFactory                product
     * @param SpecialpriceFactory                                    $specialPriceMapFactory        special price
     * @param ProductRepositoryInterface                             $productRepository             product repository
     * @param ResourceModel\Customer\CollectionFactory               $spPriceCollection             special price
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter   $extensibleDataObjectConverter extensible data object converter
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ResourceModel\SpecialpriceFactory $specialPriceResourceFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory $specialPriceMap,
        \Appseconnect\B2BMage\Model\PriceFactory $pricelistPriceFactory,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperSpecialPrice,
        \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCustomerFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Model\SpecialpriceFactory $specialPriceMapFactory,
        ProductRepositoryInterface $productRepository,
        \Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory $spPriceCollection,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
    
        $this->specialPriceResourceFactory = $specialPriceResourceFactory;
        $this->specialPriceMapFactory = $specialPriceMapFactory;
        $this->specialPriceCustomerFactory = $specialPriceCustomerFactory;
        $this->pricelistPriceFactory = $pricelistPriceFactory;
        $this->helperSpecialPrice = $helperSpecialPrice;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->specialPriceMapCollectionFactory = $specialPriceMap;
        $this->specialPriceCollection = $spPriceCollection;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * To get special price by its id
     *
     * @param int $specialpriceId special price id
     *
     * @return SpecialPriceInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($specialpriceId)
    {
        $specialPriceCollection = $this->specialPriceCollection
            ->create()->addFieldToFilter('id', $specialpriceId)->load()->getData();
        $resultOutput = $specialPriceCollection[0];
        if (empty($resultOutput)) {
            throw new CouldNotSaveException(__("Special price doesn't exist ", $specialpriceId));
        }
        // load product detail
        $specialPriceMapCollection = $this->specialPriceMapCollectionFactory
            ->create()
            ->addFieldToFilter('parent_id', $resultOutput['id']);
        $result = $specialPriceMapCollection->getData();
        $resultOutput['product_details'] = $result;
        $output [] = $resultOutput;
        return $output;
    }

    /**
     * Validate data
     *
     * @param $id                     id
     * @param $specialprice           special price
     * @param $specialPriceCollection special price collection
     *
     * @return void
     */
    public function validateData($id, $specialprice, $specialPriceCollection)
    {
        if (empty($specialPriceCollection->getData())) {
            throw new CouldNotSaveException(__("Special price doesn't exist ", $id));
        }
        
        $specialPriceCollection = $this->specialPriceCustomerFactory->create()->load($id);
        if (! $specialPriceCollection->getData()) {
            throw new CouldNotSaveException(__("SpecialPrice ID doesn't exist", $id));
        }
        $this->validateDate($specialprice);
    }

    /**
     * Update special price.
     *
     * @param SpecialPriceInterface $specialprice special price
     *
     * @return SpecialPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update(SpecialPriceInterface $specialprice)
    {
        $id = $specialprice->getId();
        $productFactory = $this->productFactory->create();
        
        $pricelistModel = $this->pricelistPriceFactory->create();
        
        $specialPriceCustomerFactory = $this->specialPriceCustomerFactory->create();
        $specialPriceCollection = $this->specialPriceCollection->create()->addFieldToFilter('id', $id);
        
        $this->validateData($id, $specialprice, $specialPriceCollection);
        
        $customerName = $this->validCustomer($specialprice->getCustomerId(), $id);
        
        $specialpriceArray = $this->extensibleDataObjectConverter
            ->toNestedArray($specialprice, [], 'Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface');
        
        $specialpriceArray['customer_name'] = $customerName;
        
        if (isset($specialpriceArray['pricelist_id']) && $specialpriceArray['pricelist_id'] > 0) {
            $pricelist = $pricelistModel->load($specialpriceArray['pricelist_id']);
            if (! $pricelist->getId()) {
                throw new InputException(__("Pricelist ID do not exist", $specialpriceArray['pricelist_id']));
            }
        }
        
        $this->specialPriceResourceFactory->create()->removeMapping($id);
        $productDetails = $this->processProducts($specialpriceArray, $id);
        // end
        unset($specialpriceArray['product_details']);
        
        if (! isset($specialpriceArray['pricelist_id'])) {
            $specialpriceArray['pricelist_id'] = 0;
        }
        if (! isset($specialpriceArray['discount_type'])) {
            $specialpriceArray['discount_type'] = 1;
        }
        if (! isset($specialpriceArray['is_active'])) {
            $specialpriceArray['is_active'] = 1;
        }
        
        $specialPriceCustomerFactory->setData($specialpriceArray)->save();
        
        $specialprice->setProductDetails($productDetails);
        // end
        return $specialprice;
    }

    /**
     * Process product
     *
     * @param $specialpriceArray special price array
     * @param $id                id
     *
     * @return array
     */
    public function processProducts($specialpriceArray, $id)
    {
        $productDataArray = [];
        if (isset($specialpriceArray['product_details'])) {
            foreach ($specialpriceArray['product_details'] as $product) {
                $product['parent_id'] = $id;
                unset($product['id']);
                
                if (empty($product['product_sku'])) {
                    $product['error'] = 'Both product_sku required.';
                    $productDataArray[] = $product;
                    continue;
                }
                
                try {
                    $productModel = $this->productRepository->get($product['product_sku']);
                } catch (NoSuchEntityException $e) {
                    $product['error'] = 'Product do not exist.';
                    $productDataArray[] = $product;
                    continue;
                }
                
                if ($productModel->getSku() == $product['product_sku']) {
                    $product['product_id'] = $productModel->getId();
                    $productDataArray[] = $this->saveSpecialPriceMapFactory($product);
                } else {
                    $product['error'] = 'Product do not exist.';
                    $productDataArray[] = $product;
                }
            }
        }
        return $productDataArray;
    }
    
    /**
     * Save special price map
     *
     * @param array $product product
     *
     * @return array
     */
    public function saveSpecialPriceMapFactory($product)
    {
        $specialPriceMapFactory = $this->specialPriceMapFactory->create();
        $specialPriceMapFactory->setData($product);
        $specialPriceMapFactory->save();
        $product['id'] = $specialPriceMapFactory->getId();
        return $product;
    }

    /**
     * Valid customer
     *
     * @param $customerId     customer id
     * @param null $specialPriceId special price id
     *
     * @return string
     */
    public function validCustomer($customerId, $specialPriceId = null)
    {
        if ($customerId) {
            $customerData = $this->customerFactory->create()->load($customerId);
            
            if (! $customerData->getData('firstname') || $customerData->getData('customer_type') != 4) {
                throw new CouldNotSaveException(
                    __("Customer ID provided does not belong to B2B Customer ID", $customerId)
                );
            }
            
            $result = $this->helperSpecialPrice->getAssignedCustomerId(null, $customerId);
            if (! empty($result) && $specialPriceId == null) {
                throw new CouldNotSaveException(
                    __("This customer already assign to special price", $customerId)
                );
            } elseif (! empty($result) && $specialPriceId != $result[0]['id']) {
                throw new CouldNotSaveException(
                    __("This customer already assign to special price", $customerId)
                );
            }
            return $customerData->getData('firstname') . ' ' . $customerData->getData('lastname');
        }
    }

    /**
     * Validate date
     *
     * @param $specialprice special price
     *
     * @return void
     */
    public function validateDate($specialprice)
    {
        
        if ($specialprice->getEndDate() !== null && $specialprice->getEndDate() < $specialprice->getStartDate()) {
            throw new CouldNotSaveException(
                __("End date should not be null and not less than start date", $specialprice->getStartDate())
            );
        }
    }

    /**
     * Add special price.
     *
     * @param SpecialPriceInterface $specialprice special price
     *
     * @return SpecialPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(SpecialPriceInterface $specialprice)
    {
        $specialPriceArray = [];
        $specialPriceArray = $this->extensibleDataObjectConverter
            ->toNestedArray($specialprice, [], 'Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface');
        $pricelistModel = $this->pricelistPriceFactory->create();
        $this->fieldRequired($specialPriceArray);
        $this->validateDate($specialprice);
        $customerName = $this->validCustomer($specialPriceArray['customer_id']);
        $specialPriceArray['customer_name'] = $customerName;
        if (! isset($specialPriceArray['pricelist_id'])) {
            $specialPriceArray['pricelist_id'] = 0;
        }
        if (! isset($specialPriceArray['discount_type'])) {
            $specialPriceArray['discount_type'] = 1;
        }
        if (! isset($specialPriceArray['is_active'])) {
            $specialPriceArray['is_active'] = 1;
        }
        if (isset($specialPriceArray['pricelist_id']) && $specialPriceArray['pricelist_id'] > 0) {
            $pricelist = $pricelistModel->load($specialPriceArray['pricelist_id']);
            if (! $pricelist->getId()) {
                throw new InputException(__("Pricelist ID do not exist", $specialPriceArray['pricelist_id']));
            }
        }
        $specialPriceCollection = $this->specialPriceCustomerFactory->create();
        $specialPriceCollection->setData($specialPriceArray);
        $specialPriceCollection->save();
        $specialPriceId = $specialPriceCollection->getId();
        if ($specialPriceId && isset($specialPriceArray['product_details'])) {
            $productDataArray = $this->getProductDataArray(
                $specialPriceArray['product_details'],
                $specialPriceId
            );
            $specialPriceCollection->setProductDetails($productDataArray);
        }
        return $specialPriceCollection;
    }

    /**
     * Get product data array
     *
     * @param $specialPriceProductData special price product data
     * @param $specialPriceId          special price id
     *
     * @return array
     */
    public function getProductDataArray($specialPriceProductData, $specialPriceId)
    {
        $productSkuCheck = [];
        $productDataArray = [];
        foreach ($specialPriceProductData as $productData) {
            if (empty($productData['product_sku']) || empty($productData['special_price'])) {
                $productData['error'] = 'Both product_sku and special_price required';
                $productDataArray[] = $productData;
                continue;
            }
            if (in_array($productData['product_sku'], $productSkuCheck)) {
                $productData['error'] = 'SKU already exist for this special price';
                $productDataArray[] = $productData;
                continue;
            }
            try {
                $productModel = $this->productRepository->get($productData['product_sku']);
            } catch (NoSuchEntityException $e) {
                $productData['error'] = 'product doesnot exist';
                $productDataArray[] = $productData;
                continue;
            }
            if ($productModel->getSku() == $productData['product_sku']) {
                $productData['parent_id'] = $specialPriceId;
                $productData['product_id'] = $productModel->getId();
                $productDataArray[] = $this->saveSpecialProductCollection($productData);
                $productSkuCheck[] = $productData['product_sku'];
            } else {
                $productData['error'] = 'Product doesnot exist';
                $productDataArray[] = $productData;
            }
        }
        return $productDataArray;
    }

    /**
     * Save special price product collection
     *
     * @param $productData product data
     *
     * @return mixed
     */
    public function saveSpecialProductCollection($productData)
    {
        $specialProductCollection = $this->specialPriceMapFactory->create();
        $specialProductCollection->setData($productData);
        $specialProductCollection->save();
        return $specialProductCollection->getData();
    }

    /**
     * Assign product
     *
     * @param $specialPriceProducts special price product
     * @param null  $specialPriceId       special price id
     * @param false $isAdmin              is admin
     *
     * @return array
     */
    public function assignProducts($specialPriceProducts, $specialPriceId = null, $isAdmin = false)
    {
        if ($isAdmin && $specialPriceId) {
            $this->specialPriceResourceFactory->create()->removeMapping($specialPriceId);
        }
        
        $productDataArray = [];
        $productSkuCheck = [];
        if (isset($specialPriceProducts['product_details'])) {
            foreach ($specialPriceProducts['product_details'] as $productData) {
                if (empty($productData['product_sku']) || empty($productData['special_price'])) {
                    $productData['error'] = 'Both product_sku and special_price required';
                    $productDataArray[] = $productData;
                    continue;
                }
                if (in_array($productData['product_sku'], $productSkuCheck)) {
                    $productData['error'] = 'SKU already exist for this special price.';
                    $productDataArray[] = $productData;
                    continue;
                }
                
                $productModel = $this->productRepository->get($productData['product_sku']);
                
                if (! $productModel) {
                    $productData['error'] = 'Product do not exist.';
                    $productDataArray[] = $productData;
                    continue;
                }
                
                if ($productModel->getSku() == $productData['product_sku']) {
                    $productData['parent_id'] = $specialPriceId;
                    $productDataArray[] = $this->saveSpecialProductCollection($productData);
                    $productSkuCheck[] = $productData['product_sku'];
                } else {
                    $productData['error'] = 'Product do not exist.';
                    $productDataArray[] = $productData;
                }
            }
        }
        return $productDataArray;
    }

    /**
     * Field required
     *
     * @param $specialPriceArray special price array
     *
     * @return void
     */
    public function fieldRequired($specialPriceArray)
    {
        $required = [];
        if (! isset($specialPriceArray['website_id']) || empty($specialPriceArray['website_id'])) {
            $required[] = "website_id";
        }
        if (! isset($specialPriceArray['customer_id']) || empty($specialPriceArray['customer_id'])) {
            $required[] = "customer_id";
        }
        if (! isset($specialPriceArray['start_date']) || empty($specialPriceArray['start_date'])) {
            $required[] = "start_date";
        }
        if (! isset($specialPriceArray['end_date']) || empty($specialPriceArray['end_date'])) {
            $required[] = "end_date";
        }
        if (! empty($required)) {
            throw new CouldNotSaveException(__("This fields required", implode(',', $required)));
        }
    }
}
