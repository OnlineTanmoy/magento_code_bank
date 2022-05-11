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
namespace Appseconnect\B2BMage\Api\CustomerSpecialPrice;

use Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface;

/**
 * Interface SpecialPriceProductInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface SpecialPriceRepositoryInterface
{

    /**
     * Loads special price.
     *
     * @param int $specialpriceId specil price id
     *
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface
     */
    public function get($specialpriceId);

    /**
     * Update special price.
     *
     * @param SpecialPriceInterface $specialprice special price
     *
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface
     */
    public function update(SpecialPriceInterface $specialprice);

    /**
     * Add special price.
     *
     * @param SpecialPriceInterface $specialprice special price
     * 
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceInterface
     */
    public function create(SpecialPriceInterface $specialprice);
}
