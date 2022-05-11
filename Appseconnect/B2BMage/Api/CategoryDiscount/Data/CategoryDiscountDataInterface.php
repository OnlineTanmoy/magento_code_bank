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
 * Interface CategoryDiscountDataInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CategoryDiscountDataInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CATEGORYDISCOUNT_ID = 'categorydiscount_id';

    /**
     * Category Id
     */
    const CATEGORY_ID = 'category_id';

    /**
     * Discount factor
     */
    const DISCOUNT_FACTOR = 'discount_factor';

    /**
     * Is active
     */
    const IS_ACTIVE = 'is_active';

    /**
     * Discount type
     */
    const DISCOUNT_TYPE = 'discount_type';
    
    /**
     * Get category discount id
     *
     * @return int|null
     */
    public function getCategorydiscountId();

    /**
     * Set category discount id
     *
     * @param int $categoryDiscountId category discount id
     *
     * @return $this
     */
    public function setCategorydiscountId($categoryDiscountId);

    /**
     * Get category id
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Set category id
     *
     * @param int $categoryId category id
     *
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Get discount factor
     *
     * @return string|null
     */
    public function getDiscountFactor();

    /**
     * Set discount factor
     *
     * @param string $discountFactor discount factor
     *
     * @return $this
     */
    public function setDiscountFactor($discountFactor);

    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get discount type
     *
     * @return int|null
     */
    public function getDiscountType();

    /**
     * Set discount type
     *
     * @param int $discountType
     * @return $this
     */
    public function setDiscountType($discountType);
}
