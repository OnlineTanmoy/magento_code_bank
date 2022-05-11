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
namespace Appseconnect\B2BMage\Api\CategoryDiscount;

use Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface;
use Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface;

/**
 * Interface CustomerCategoryDiscountRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CustomerCategoryDiscountRepositoryInterface
{

    /**
     * Create customer specific category discount.
     *
     * @param CategoryDiscountInterface $categoryDiscount category discount
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomerCategoryDiscount(CategoryDiscountInterface $categoryDiscount);

    /**
     * Get customer specific category discounts.
     *
     * @param int $customerId customer id
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerCategoryDiscount($customerId);

    /**
     * Update customer specific category discount.
     *
     * @param CategoryDiscountInterface $categoryDiscount category discount
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCustomerCategoryDiscount(CategoryDiscountInterface $categoryDiscount);
}
