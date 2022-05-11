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
namespace Appseconnect\B2BMage\Api\CustomerTierPrice;

/**
 * Interface CustomerTierpriceRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CustomerTierpriceRepositoryInterface
{

    /**
     * Create customer specific tier price.
     *
     * @param \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface $tierPrice tier price
     *
     * @return \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface $tierPrice
    );
    
    /**
     * Update customer specific tier price.
     *
     * @param \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface $tierPrice tier price
     *
     * @return \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update(
        \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface $tierPrice
    );

    /**
     * Get customer specific tier price.
     *
     * @param int $customerId customer id
     * 
     * @return \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\CustomerTierpriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);
}
