<?php

/**
 * Namespace
 *
 * @category Appseconnect
 * @package  B2BMage
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\Catalog\Data;

/**
 * Product Data Interface
 *
 * @api
 * @category API
 * @package  B2BMage
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductTierPriceInterface
{
    const QUANTITY = 'quantity';

    const TIER_PRICE = 'tier_price';

    /**
     * Get Quantity
     *
     * @return int|null
     */
    public function getQuantity();

    /**
     * Set Quantity
     *
     * @param int $qty Quantity
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
     * @param double $tierPrice Tier Price
     *
     * @return $this
     */
    public function setTierPrice($tierPrice);

}
