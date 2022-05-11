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
namespace Appseconnect\B2BMage\Model;

/**
 * Class ProductTierPrice
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductTierPrice implements \Appseconnect\B2BMage\Api\Catalog\Data\ProductTierPriceInterface
{
    /**
     * Get Quantity
     *
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->getData(self::QUANTITY);
    }

    /**
     * Set Quantity
     *
     * @param int $qty qty
     *
     * @return $this
     */
    public function setQuantity($qty)
    {
        return $this->setData(self::QUANTITY, $qty);
    }

    /**
     * Get Tier Price
     *
     * @return double|null
     */
    public function getTierPrice()
    {
        return $this->getData(self::TIER_PRICE);
    }

    /**
     * Set Tier Price
     *
     * @param double $tierPrice tier price
     *
     * @return $this
     */
    public function setTierPrice($tierPrice)
    {
        return $this->setData(self::TIER_PRICE, $tierPrice);
    }
}
