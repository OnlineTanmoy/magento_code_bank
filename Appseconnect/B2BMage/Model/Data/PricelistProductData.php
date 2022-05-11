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
 * Class PricelistProductData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class PricelistProductData extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Pricelist\Data\ProductDataInterface
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
     * @return double|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set Sku
     *
     * @param int $sku Sku
     *
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Set Price
     *
     * @param double $price Price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }


    /**
     * Set is manual
     *
     * @param int $isManual is manual
     *
     * @return $this
     */
    public function setIsManual($isManual)
    {
        return $this->setData(self::IS_MANUAL, $isManual);
    }

    /**
     * Get is manual
     *
     * @return double|null
     */
    public function getIsManual()
    {
        return $this->_get(self::IS_MANUAL);
    }
}
