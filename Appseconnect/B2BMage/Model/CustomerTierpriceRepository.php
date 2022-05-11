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

use Appseconnect\B2BMage\Api\CustomerTierPrice\CustomerTierpriceRepositoryInterface;
use Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface;
use Appseconnect\B2BMage\Model\Data\CustomerTierprice;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class CustomerTierpriceRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerTierpriceRepository implements CustomerTierpriceRepositoryInterface
{
    /**
     * Tier price
     *
     * @var CustomerTierpriceInterface
     */
    public $tierPriceFactory;

    /**
     * Tier price resource
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\TierpriceFactory
     */
    public $tierPriceResourceFactory;

    /**
     * Extensible data object converter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * Website collection
     *
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollection;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Pricelist
     *
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPrice;

    /**
     * Trierprice
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory
     */
    public $tierPriceCollection;

    /**
     * Customer tier price
     *
     * @var \Appseconnect\B2BMage\Model\ProductFactory
     */
    public $customerTierPriceProduct;

    /**
     * Customer tier price tier
     *
     * @var \Appseconnect\B2BMage\Model\TierpriceFactory
     */
    public $customerTierPriceTier;

    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Tier price collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Tierprice\CollectionFactory
     */
    public $tierPriceTierCollection;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $productCollection;

    /**
     * CustomerTierpriceRepository constructor.
     *
     * @param ResourceModel\TierpriceFactory                                 $tierPriceResourceFactory      tier price resource
     * @param CustomerTierpriceInterface                                     $tierPriceFactory              tier price
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter           $extensibleDataObjectConverter extensible data object converter
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory   $websiteCollection             website collection
     * @param \Magento\Customer\Model\CustomerFactory                        $customerFactory               customer
     * @param PriceFactory                                                   $pricelistPrice                pricelist price
     * @param ResourceModel\Product\CollectionFactory                        $tierPriceCollection           tier price collection
     * @param ProductFactory                                                 $customerTierPriceProduct      customer tire price product
     * @param TierpriceFactory                                               $customerTierPriceTier         customer tier price
     * @param \Magento\Catalog\Model\ProductFactory                          $productFactory                product
     * @param ResourceModel\Tierprice\CollectionFactory                      $tierPriceTier                 tier price
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection             product collection
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ResourceModel\TierpriceFactory $tierPriceResourceFactory,
        CustomerTierpriceInterface $tierPriceFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\PriceFactory $pricelistPrice,
        \Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory $tierPriceCollection,
        \Appseconnect\B2BMage\Model\ProductFactory $customerTierPriceProduct,
        \Appseconnect\B2BMage\Model\TierpriceFactory $customerTierPriceTier,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Tierprice\CollectionFactory $tierPriceTier,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ) {
        $this->tierPriceFactory = $tierPriceFactory;
        $this->tierPriceResourceFactory = $tierPriceResourceFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->websiteCollection = $websiteCollection;
        $this->customerFactory = $customerFactory;
        $this->pricelistPrice = $pricelistPrice;
        $this->tierPriceCollection = $tierPriceCollection;
        $this->customerTierPriceProduct = $customerTierPriceProduct;
        $this->customerTierPriceTier = $customerTierPriceTier;
        $this->productFactory = $productFactory;
        $this->tierPriceTierCollection = $tierPriceTier;
        $this->productCollection = $productCollection;
    }

    /**
     * Get website
     *
     * @return array
     */
    public function getWebsite()
    {
        $website = $this->websiteCollection->create();
        $output = $website->getData();
        $result = [];
        foreach ($output as $val) {
            $result [$val ['website_id']] = $val ['name'];
        }
        return $result;
    }

    /**
     * Check require field
     *
     * @param mixed $requiredFields require field
     * @param mixed $data           data
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function checkRequiredFields($requiredFields, $data)
    {
        foreach ($requiredFields as $requiredValues) {
            // Checking for empty field values
            if (isset($data[$requiredValues]) && trim($data[$requiredValues]) == '') {
                throw new \Magento\Framework\Exception\InputException(
                    __('[' . $requiredValues . '] can not be empty.')
                );
            }
            // Checking for missing fields
            if (!isset($data[$requiredValues])) {
                throw new \Magento\Framework\Exception\InputException(
                    __('Field [' . $requiredValues . '] is required.')
                );
            }
        }
    }

    /**
     * Is website is exists
     *
     * @param array $data data
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function isWebsiteIdExist($data)
    {
        $websiteIds = $this->getWebsite();
        foreach ($websiteIds as $key => $websiteValues) {
            $checkWebsiteId = false;
            if (!array_key_exists($data['website_id'], $websiteIds)) {
                $checkWebsiteId = true;
            }
            break;
        }
        if ($checkWebsiteId) {
            throw new \Magento\Framework\Exception\InputException(
                __('Website ID [' . $data['website_id'] . '] does not exist.')
            );
        }
    }

    /**
     * Is b2b customer
     *
     * @param array $data data
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function isB2BCustomer($data)
    {
        $customerData = $this->customerFactory->create()->load($data['customer_id']);
        // for valid customer
        if (!$customerData->getData('firstname') || $customerData->getData('customer_type') != 4) {
            throw new CouldNotSaveException(
                __("Customer ID is not B2B type", $data['customer_id'])
            );
        }
    }

    /**
     * Is valid price list
     *
     * @param array $data data
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function isValidPricelist($data)
    {
        $parentPricelistModel = $this->pricelistPrice->create()->load($data['pricelist_id']);
        if (!$parentPricelistModel->getId()) {
            throw new \Magento\Framework\Exception\InputException(
                __('Pricelist ID [' . $data['pricelist_id'] . '] does not exist.')
            );
        }
    }

    /**
     * Data validator
     *
     * @param array $data data
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function dataValidator($data)
    {
        $requiredFields[] = 'customer_id';
        $requiredFields[] = 'website_id';
        $requiredFields[] = 'pricelist_id';
        $requiredFields[] = 'discount_type';
        $requiredFields[] = 'is_active';
        $this->checkRequiredFields($requiredFields, $data);
        // Tier price information validation
        foreach ($requiredFields as $field) {
            if ($field == 'website_id') {
                $checkWebsiteId = $this->isWebsiteIdExist($data);
            }
            if ($field == 'customer_id') {
                $this->isB2BCustomer($data);
            } elseif ($field == 'pricelist_id' && $data['pricelist_id'] > 0) {
                $this->isValidPricelist($data);
            } elseif ($field == 'discount_type') {
                if ($data['discount_type'] != 0 && $data['discount_type'] != 1) {
                    throw new \Magento\Framework\Exception\InputException(
                        __('Discount Type must be 0 or 1.')
                    );
                }
            } elseif ($field == 'is_active') {
                if ($data['is_active'] != 0 && $data['is_active'] != 1) {
                    throw new \Magento\Framework\Exception\InputException(
                        __('Status should have binary values.')
                    );
                }
            }
        }
    }

    /**
     * Product data validator
     *
     * @param array $prodData           product data
     * @param array $tierPriceDataArray tier price data array
     *
     * @return array
     */
    public function productDataValidator($prodData, $tierPriceDataArray)
    {
        if (!isset($prodData['product_sku']) || !isset($prodData['quantity']) || !isset($prodData['tier_price'])) {
            $prodData['error'] = '[product_sku],[quantity] and [tier_price] are required fields';
        } elseif ($prodData['quantity'] <= 0 || $prodData['quantity'] >= 99999999) {
            $prodData['error'] = 'Quantity must be in between 0 and 100000000';
        } elseif (isset($tierPriceDataArray['discount_type']) && $prodData['tier_price']) {
            if ($tierPriceDataArray['discount_type'] == 1 && ($prodData['tier_price'] < 0 || $prodData['tier_price'] > 100)) {
                $prodData['error'] = 'Tier price cannot be < 0 or > 100 when discount type 1 for SKU[' . $prodData['product_sku'] . ']';
            } elseif ($tierPriceDataArray['discount_type'] == 0 && $prodData['tier_price'] < 0) {
                $prodData['error'] = 'Tier price cannot be < 0 when discount type 0 for SKU[' . $prodData['product_sku'] . ']';
            }
        }
        return $prodData;
    }

    /**
     * Save
     *
     * @param CustomerTierpriceInterface $tierPrice tire price
     *
     * @return CustomerTierpriceInterface
     */
    public function save(CustomerTierpriceInterface $tierPrice)
    {
        $customerId = $tierPrice->getCustomerId();
        $customerName = $this->customerFactory->create()
            ->load($customerId)
            ->getName();
        $tierPriceDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($tierPrice, [], 'Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface');
        $validate = $this->dataValidator($tierPriceDataArray);
        if (!($this->customerFactory->create()->load($customerId)->getEntityId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        } elseif ($this->tierPriceCollection->create()->addFieldToFilter('customer_id', $customerId)->getData()
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID is already associated", $customerId)
            );
        }
        $tierPriceModel = $this->customerTierPriceProduct->create();
        $tierPriceDataArray['customer_name'] = $customerName;
        $tierPriceModel->setData($tierPriceDataArray);
        $tierPriceModel->save();
        $tierPriceId = $tierPriceModel->getId();
        $tierPrice->setId($tierPriceId);
        $result = [];
        if (isset($tierPriceDataArray['product_data']) && $tierPriceDataArray['product_data']) {
            foreach ($tierPriceDataArray['product_data'] as $productData) {
                $validateData = $this->productDataValidator($productData, $tierPriceDataArray);
                if (!empty($validateData['error'])) {
                    $result[] = $validateData;
                    continue;
                }
                if ($productData['product_sku']) {
                    $checkProductExists = $this->getProductIdBySku($productData);
                    $sku = null;
                    if ($checkProductExists) {
                        $checkProductData = $this->getProductDataById($checkProductExists);
                        $sku = $checkProductData->getSku();
                    }
                    if (!$checkProductExists || strcmp($productData['product_sku'], $sku)) {
                        $productData['error'] = 'SKU [' . $productData['product_sku'] . '] does not exist.';
                        $result[] = $productData;
                        continue;
                    } else {
                        $productData['parent_id'] = $tierPriceId;
                        $result[] = $this->tierPriceProductMapModelSave($productData);
                    }
                }
            }

            if ($result) {
                $tierPrice->setProductData($result);
            }
        }

        return $tierPrice;
    }

    /**
     * Tier price product map model save
     *
     * @param array $productData product data
     *
     * @return array
     */
    public function tierPriceProductMapModelSave($productData)
    {
        $tierPriceProductMapModel = $this->customerTierPriceTier->create();
        $tierPriceProductMapModel->setData($productData);
        $tierPriceProductMapModel->save();
        return $tierPriceProductMapModel->getData();
    }

    /**
     * Get product id by sku
     *
     * @param array $productData product data
     *
     * @return int
     */
    public function getProductIdBySku($productData)
    {
        $itemId = $this->productFactory->create()->getIdBySku($productData['product_sku']);
        return $itemId;
    }

    /**
     * Get product data by id
     *
     * @param int $checkProductExists check product exist
     *
     * @return mixed
     */
    public function getProductDataById($checkProductExists)
    {
        $productLoad = $this->productFactory->create()->load($checkProductExists);
        return $productLoad;
    }

    /**
     * Tier price product model save
     *
     * @param array $productData product data
     *
     * @return array
     */
    public function tierPriceProductModelSave($productData)
    {
        $tierPriceProductModel = $this->customerTierPriceTier->create();
        $tierPriceProductModel->setData($productData);
        $tierPriceProductModel->save();
        return $tierPriceProductModel->getData();
    }

    /**
     * Update
     *
     * @param CustomerTierpriceInterface $tierPrice tierprice
     *
     * @return CustomerTierpriceInterface
     */
    public function update(CustomerTierpriceInterface $tierPrice)
    {
        $tierPriceId = $tierPrice->getId();
        $customerId = $tierPrice->getCustomerId();
        if (!isset($customerId)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__("Customer ID is a required field"));
        }
        $tierPriceIdExist = $this->tierPriceCollection
            ->create()
            ->addFieldToFilter('id', $tierPriceId)
            ->getData() ? true : false;
        if (!$tierPriceIdExist) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Tier Price ID doesn't exist", $tierPriceId)
            );
        } else {
            $tierPriceExist = $this->tierPriceCollection
                ->create()
                ->addFieldToFilter('id', $tierPriceId)
                ->getData() ? true : false;
            if ($tierPriceExist) {
                $customerName = $this->customerFactory->create()
                    ->load($customerId)
                    ->getName();
                $tierPriceDataArray = $this->extensibleDataObjectConverter
                    ->toNestedArray(
                        $tierPrice,
                        [],
                        'Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface'
                    );
                $tierPriceModel = $this->customerTierPriceProduct->create();
                $tierPriceDataArray['customer_name'] = $customerName;
                $tierPriceModel->setData($tierPriceDataArray);
                $tierPriceModel->save();

                if (isset($tierPriceDataArray['product_data']) && !empty($tierPriceDataArray['product_data'])) {
                    $result = $this->saveProductData($tierPriceDataArray, $tierPriceId);
                } else {
                    return $tierPrice;
                }
            } else {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __("Tier Price ID[" . $tierPriceId . "] do not exist for Customer ID[" . $customerId . ']')
                );
            }
        }
        $tierPrice->setProductData($result);
        return $tierPrice;
    }

    /**
     * Save product data
     *
     * @param array $tierPriceDataArray tier price data
     * @param int   $tierPriceId        tier price id
     *
     * @return \Appseconnect\B2BMage\Model\unknown[]|string[]
     */
    public function saveProductData($tierPriceDataArray, $tierPriceId)
    {
        $this->tierPriceResourceFactory->create()->removeMapping($tierPriceId);
        $result = [];
        foreach ($tierPriceDataArray['product_data'] as $productData) {
            $validateData = $this->productDataValidator($productData, $tierPriceDataArray);
            if (!empty($validateData['error'])) {
                $result[] = $validateData;
                continue;
            }
            if ($productData['product_sku']) {
                $productCollection = $this->productCollection;
                $collection = $productCollection->create();
                $checkProductExists = $this->getProductIdBySku($productData);
                if (!$checkProductExists) {
                    $productData['error'] = 'SKU [' . $productData['product_sku'] . '] does not exist.';
                    $result[] = $productData;
                    continue;
                }
                $productData['parent_id'] = $tierPriceId;
                $result[] = $this->tierPriceProductModelSave($productData);
            }
        }

        return $result;
    }

    /**
     * Get by customer id
     *
     * @param int $customerId customer id
     *
     * @return CustomerTierpriceInterface
     */
    public function getByCustomerId($customerId)
    {
        if (!($this->customerFactory->create()->load($customerId)->getEntityId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__("Customer ID doesn't exist", $customerId));
        }
        $tierPriceExist = $this->tierPriceCollection
            ->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getData() ? true : false;
        if ($tierPriceExist) {
            $tpPriceCollection = $this->tierPriceCollection
                ->create()
                ->addFieldToFilter('customer_id', $customerId)->load()->getData();
            $tierPriceCollection = $tpPriceCollection[0];
            $tierPriceProductCollection = $this->tierPriceTierCollection
                ->create()
                ->addFieldToFilter('parent_id', $tierPriceCollection['id'])
                ->getData();
            $this->tierPriceFactory->setId($tierPriceCollection['id']);
            $this->tierPriceFactory->setWebsiteId($tierPriceCollection['website_id']);
            $this->tierPriceFactory->setCustomerId($tierPriceCollection['customer_id']);
            $this->tierPriceFactory->setPricelistId($tierPriceCollection['pricelist_id']);
            $this->tierPriceFactory->setDiscountType($tierPriceCollection['discount_type']);
            $this->tierPriceFactory->setIsActive($tierPriceCollection['is_active']);
            $this->tierPriceFactory->setProductData($tierPriceProductCollection);
            return $this->tierPriceFactory;
        } else {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("No Tier Price exist Customer ID", $customerId)
            );
        }
    }
}
