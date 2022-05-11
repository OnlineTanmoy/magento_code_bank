<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\CustomerTierPrice\Data;

/**
 * Interface CustomerTierpriceInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductDataInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const PARENT_ID = 'parent_id';

    const PRODUCT_SKU = 'product_sku';

    const QUANTITY = 'quantity';

    const TIER_PRICE = 'tier_price';

    const ERROR = 'error';

    /**
     * Get Parent Id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set Parent Id
     *
     * @param int $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getProductSku();

    /**
     * Set Product Sku
     *
     * @param int $sku sku
     *
     * @return $this
     */
    public function setProductSku($sku);

    /**
     * Get Quantity
     *
     * @return int|null
     */
    public function getQuantity();

    /**
     * Set Quantity
     *
     * @param int $qty qty
     *
     * @return $this
     */
    public function setQuantity($qty);

    /**
     * Get Tier Price
     *
     * @return double|null
     */
    public function getTierPrice();

    /**
     * Set Tier Price
     *
     * @param double $tierPrice tier price
     *
     * @return $this
     */
    public function setTierPrice($tierPrice);

    /**
     * Get Error
     *
     * @return string|null
     */
    public function getError();

    /**
     * Set Error
     *
     * @param string $error error
     *
     * @return $this
     */
    public function setError($error);
}
