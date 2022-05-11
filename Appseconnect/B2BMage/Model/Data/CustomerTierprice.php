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
namespace Appseconnect\B2BMage\Model\Data;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class CustomerTierprice
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerTierprice extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }
    
    /**
     * Get Pricelist Id
     *
     * @return int|null
     */
    public function getPricelistId()
    {
        return $this->_get(self::PRICELIST_ID);
    }
    
    /**
     * Get discount type
     *
     * @return int|null
     */
    public function getDiscountType()
    {
        return $this->_get(self::DISCOUNT_TYPE);
    }
    
    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }
    
    /**
     * Get product data
     *
     * @return \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\ProductDataInterface[]|null
     */
    public function getProductData()
    {
        return $this->_get(self::PRODUCT_DATA);
    }
    
    /**
     * Set id
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set website id
     *
     * @param int $websiteId WebsiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Set Customer id
     *
     * @param int $customerId CustomerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
     * Set Pricelist Id
     *
     * @param int $pricelistId PricelistId
     *
     * @return $this
     */
    public function setPricelistId($pricelistId)
    {
        return $this->setData(self::PRICELIST_ID, $pricelistId);
    }
    
    /**
     * Set discount type
     *
     * @param int $discountType DiscountType
     *
     * @return $this
     */
    public function setDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }
    
    /**
     * Set is active
     *
     * @param int $isActive IsActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
    
    /**
     * Set product data
     *
     * @param \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\ProductDataInterface[] $productData ProductData
     *
     * @return $this
     */
    public function setProductData($productData)
    {
        return $this->setData(self::PRODUCT_DATA, $productData);
    }
}
