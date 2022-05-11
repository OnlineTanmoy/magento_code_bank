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
namespace Appseconnect\B2BMage\Api\Sales\Data;

/**
 * Interface ProductDataInterface
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
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const SKU = 'sku';
    
    const PRICE = 'price';

    const QTY = 'qty';
    /**
     * #@-
     */

    /**
     * Get Sku.
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Sku.
     *
     * @param string $sku sku
     *
     * @return $this
     */
    public function setSku($sku = null);
    
    /**
     * Get Price.
     *
     * @return int|null
     */
    public function getPrice();
    
    /**
     * Set Price.
     *
     * @param int $price price
     *
     * @return $this
     */
    public function setPrice($price = null);

    /**
     * Get Qty.
     *
     * @return int|null
     */
    public function getQty();

    /**
     * Set Qty.
     *
     * @param int $qty qty
     * 
     * @return $this
     */
    public function setQty($qty = null);
}
