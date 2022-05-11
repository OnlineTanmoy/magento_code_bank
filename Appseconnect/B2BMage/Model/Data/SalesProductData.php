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

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class SalesProductData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SalesProductData extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Sales\Data\ProductDataInterface
{
    
    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }
    
    /**
     * Get Price
     *
     * @return int|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Get Qty
     *
     * @return int|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }
    
    /**
     * Set Sku
     *
     * @param string $sku Sku
     *
     * @return $this
     */
    public function setSku($sku = null)
    {
        return $this->setData(self::SKU, $sku);
    }
    
    /**
     * Set Price
     *
     * @param int $price Price
     *
     * @return $this
     */
    public function setPrice($price = null)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Set Qty
     *
     * @param int $qty Qty
     *
     * @return $this
     */
    public function setQty($qty = null)
    {
        return $this->setData(self::QTY, $qty);
    }
}
