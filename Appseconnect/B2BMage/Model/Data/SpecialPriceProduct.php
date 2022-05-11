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
 * Class SpecialPriceProduct
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SpecialPriceProduct extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface
{
    /**
     * Special price product ID
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
     * Special price product ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }
    
    /**
     * Parent id.
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setParentId($id)
    {
        return $this->setData(self::PARENT_ID, $id);
    }
    
    /**
     * Parent id.
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }
    
    /**
     * Product sku
     *
     * @param string $sku Sku
     *
     * @return $this
     */
    public function setProductSku($sku)
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }
    
    /**
     * Product sku
     *
     * @return string|null
     */
    public function getProductSku()
    {
        return $this->_get(self::PRODUCT_SKU);
    }
    
    /**
     * Set special price product amount.
     *
     * @param double $price Price
     *
     * @return $this
     */
    public function setSpecialPrice($price)
    {
        return $this->setData(self::SPECIAL_PRICE, $price);
    }
    
    /**
     * Get special price product amount.
     *
     * @return double|null
     */
    public function getSpecialPrice()
    {
        return $this->_get(self::SPECIAL_PRICE);
    }

    /**
     * SetError
     *
     * @param string $error Error
     *
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }
    
    /**
     * Error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }
}
