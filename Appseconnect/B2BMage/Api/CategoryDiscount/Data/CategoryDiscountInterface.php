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
namespace Appseconnect\B2BMage\Api\CategoryDiscount\Data;

/**
 * Interface CategoryDiscountInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CategoryDiscountInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_ID = 'customer_id';

    const CATEGORYDISCOUNT_DATA = 'categorydiscount_data';

    /**
     * #@-
     */
    
    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get category discount data
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface[]|null
     */
    public function getCategorydiscountData();

    /**
     * Set category discount data
     *
     * @param \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface[] $categorydiscountData categorydiscountdata
     *
     * @return $this
     */
    public function setCategorydiscountData(array $categorydiscountData);
}
