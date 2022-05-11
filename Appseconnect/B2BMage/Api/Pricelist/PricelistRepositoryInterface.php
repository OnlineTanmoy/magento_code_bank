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
namespace Appseconnect\B2BMage\Api\Pricelist;

use Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface;
use Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface;
use Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface;

/**
 * Interface PricelistRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface PricelistRepositoryInterface
{

    /**
     * Loads a customer specified pricelist.
     *
     * @param int $customerId customer id
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
     */
    public function get($customerId);

    /**
     * Performs upadate operations for a specified pricelist.
     *
     * @param PricelistInterface[] $pricelist price list
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
     */
    public function update(PricelistInterface $pricelist);

    /**
     * Performs assign operation of pricelist to a customer.
     *
     * @param PricelistAssignInterface $entity entity
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface
     */
    public function assignPricelist(PricelistAssignInterface $entity);

    /**
     * Performs persist operations for a specified pricelist.
     *
     * @param PricelistInterface[] $pricelist pricelist
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
     */
    public function create(PricelistInterface $pricelist);

    /**
     * Performs assign operations for products in pricelist.
     *
     * @param ProductAssignInterface[] $product product object
     * @param bool                     $isAdmin is admin
     * 
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface
     */
    public function assignProducts(ProductAssignInterface $product, $isAdmin = false);
}
