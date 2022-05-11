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
 * Class ProductData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductData extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\ProductDataInterface
{

    /**
     * Get Parent Id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getProductSku()
    {
        return $this->_get(self::PRODUCT_SKU);
    }

    /**
     * Get Quantity
     *
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->_get(self::QUANTITY);
    }

    /**
     * Get Tier Price
     *
     * @return double|null
     */
    public function getTierPrice()
    {
        return $this->_get(self::TIER_PRICE);
    }
    
    /**
     * Get Error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }

    /**
     * Set Parent Id
     *
     * @param int $parentId ParentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Set Product Sku
     *
     * @param int $sku Sku
     *
     * @return $this
     */
    public function setProductSku($sku)
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    /**
     * Set Quantity
     *
     * @param int $qty Qty
     *
     * @return $this
     */
    public function setQuantity($qty)
    {
        return $this->setData(self::QUANTITY, $qty);
    }

    /**
     * Set Tier Price
     *
     * @param double $tierPrice TierPrice
     *
     * @return $this
     */
    public function setTierPrice($tierPrice)
    {
        return $this->setData(self::TIER_PRICE, $tierPrice);
    }
    
    /**
     * Set Error
     *
     * @param string $error Error
     *
     * @return $this
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }
}
