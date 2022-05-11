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
namespace Appseconnect\B2BMage\Api\CreditLimit;

/**
 * Interface CreditLimitRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CreditLimitRepositoryInterface
{

    /**
     * Get credit limit of a B2B customer
     *
     * @param int $customerId customer id
     *
     * @return \Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customerId);

    /**
     * Add credit limit for a B2B customer
     *
     * @param \Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface $creditLimitData credit limit data
     *
     * @return \Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface $creditLimitData);

    /**
     * Update credit limit for a B2B customer
     *
     * @param \Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface $creditLimitData credit limit data
     * 
     * @return \Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update(\Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface $creditLimitData);
}
