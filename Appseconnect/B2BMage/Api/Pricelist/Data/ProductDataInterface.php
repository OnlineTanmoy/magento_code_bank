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
namespace Appseconnect\B2BMage\Api\Pricelist\Data;

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

    const SKU = 'sku';

    const PRICE = 'price';

    const IS_MANUAL = 'is_manual';

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Sku
     *
     * @param int $sku sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get Price
     *
     * @return double|null
     */
    public function getPrice();

    /**
     * Set Price
     *
     * @param double $price price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get is manual
     *
     * @return double|null
     */
    public function getIsManual();

    /**
     * Set is manual
     *
     * @param double $isManual is manual
     *
     * @return $this
     */
    public function setIsManual($isManual);

}
