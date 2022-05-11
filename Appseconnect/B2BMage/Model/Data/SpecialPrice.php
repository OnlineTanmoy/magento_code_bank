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

/**
 * Class SpecialPrice
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SpecialPrice extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface
{
    /**
     * Set special price ID
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
     * Get special price ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }
    
    /**
     * Set website ID
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setWebsiteId($id)
    {
        return $this->setData(self::WEBSITE_ID, $id);
    }
    
    /**
     * Get pricelist id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }
    
    /**
     * Set customer Id
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setCustomerId($id)
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }
    
    /**
     * Get customer Id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }
    
    /**
     * Set customer name of special price.
     *
     * @param string $name Name
     *
     * @return $this
     */
    public function setCustomerName($name)
    {
        return $this->setData(self::CUSTOMER_NAME, $name);
    }
    
    /**
     * Get customer name of special price.
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->_get(self::CUSTOMER_NAME);
    }
    
    /**
     * Set customer Id
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setPricelistId($id)
    {
        return $this->setData(self::PRICELIST_ID, $id);
    }
    
    /**
     * Get pricelist id
     *
     * @return int|null
     */
    public function getPricelistId()
    {
        return $this->_get(self::PRICELIST_ID);
    }
    
    /**
     * Set discount type.
     *
     * @param int $type Type
     *
     * @return $this
     */
    public function setDiscountType($type)
    {
        return $this->setData(self::DISCOUNT_TYPE, $type);
    }
    
    /**
     * Get discount type.
     *
     * @return int|null
     */
    public function getDiscountType()
    {
        return $this->_get(self::DISCOUNT_TYPE);
    }
    
    /**
     * Set discount type.
     *
     * @param string $date Date
     *
     * @return $this
     */
    public function setStartDate($date)
    {
        return $this->setData(self::START_DATE, $date);
    }
    
    /**
     * Get discount type.
     *
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->_get(self::START_DATE);
    }
    
    /**
     * Set discount type.
     *
     * @param string $date Date
     *
     * @return $this
     */
    public function setEndDate($date)
    {
        return $this->setData(self::END_DATE, $date);
    }
    
    /**
     * Get discount type.
     *
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->_get(self::END_DATE);
    }

    /**
     * Set discount type.
     *
     * @param int $status Status
     *
     * @return $this
     */
    public function setIsActive($status)
    {
        return $this->setData(self::IS_ACTIVE, $status);
    }
    
    /**
     * Get discount type.
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }
    
    /**
     * Get product detail
     * {@inheritdoc}
     *
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface[]|null
     */
    public function getProductDetails()
    {
        return $this->_get(self::PRODUCT_DETAILS);
    }
    
    /**
     * Set product detail
     * {@inheritdoc}
     *
     * @param \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface[] $productDetails ProductDetails
     *
     * @return $this
     */
    public function setProductDetails(array $productDetails = null)
    {
        return $this->setData(self::PRODUCT_DETAILS, $productDetails);
    }
}
