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
namespace Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data;

/**
 * Interface SpecialPriceProductInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface SpecialPriceProductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * Special price product ID
     */
    const ID = 'id';

    /**
     * Parent id.
     */
    const PARENT_ID = 'parent_id';

    /**
     * Product sku.
     */
    const PRODUCT_SKU = 'product_sku';

    /**
     * Price.
     */
    const SPECIAL_PRICE = 'special_price';

    /**
     * Error
     */
    const ERROR = 'error';

    /**
     * Get the id for special price product.
     *
     * @return int|null Pricelist Id.
     */
    public function getId();

    /**
     * Get special price id.
     *
     * @return int|null Website Id.
     */
    public function getParentId();

    /**
     * Get product sku.
     *
     * @return string|null .
     */
    public function getProductSku();

    /**
     * Get special price product amount.
     *
     * @return double|null .
     */
    public function getSpecialPrice();

    /**
     * Set the id for special price product.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Set special price id.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setParentId($id);

    /**
     * Set product sku.
     *
     * @param string $sku sku
     *
     * @return $this
     */
    public function setProductSku($sku);

    /**
     * Set amount.
     *
     * @param double $price price
     *
     * @return $this
     */
    public function setSpecialPrice($price);

    /**
     * Get error.
     *
     * @return string|null Website Id.
     */
    public function getError();

    /**
     * Set error.
     *
     * @param string $error error
     *
     * @return $this
     */
    public function setError($error);
}
