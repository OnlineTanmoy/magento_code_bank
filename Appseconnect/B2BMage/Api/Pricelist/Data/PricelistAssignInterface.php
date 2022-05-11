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
 * Interface EntityInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface PricelistAssignInterface
{

    /**
     * Customer ID
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * Pricelist ID.
     */
    const PRICELIST_ID = 'pricelist_id';

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get Pricelist Id
     *
     * @return int|null
     */
    public function getPricelistId();

    /**
     * Set Customer Id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set Pricelist Id
     *
     * @param int $pricelistId pricelist id
     *
     * @return $this
     */
    public function setPricelistId($pricelistId);
}
