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
namespace Appseconnect\B2BMage\Model\Data;

/**
 * Class CategoryDiscountData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CategoryDiscountData extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface
{

    /**
     * Get category discount id
     *
     * @return int|null
     */
    public function getCategorydiscountId()
    {
        return $this->_get(self::CATEGORYDISCOUNT_ID);
    }

    /**
     * Get category id
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }

    /**
     * Get discount factor
     *
     * @return string|null
     */
    public function getDiscountFactor()
    {
        return $this->_get(self::DISCOUNT_FACTOR);
    }

    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Get discount type
     *
     * @return int|null
     */
    public function getDiscountType()
    {
        return $this->_get(self::DISCOUNT_TYPE);
    }

    /**
     * Set category discount id
     *
     * @param int $categoryDiscountId CategoryDiscountId
     *
     * @return $this
     */
    public function setCategorydiscountId($categoryDiscountId)
    {
        return $this->setData(self::CATEGORYDISCOUNT_ID, $categoryDiscountId);
    }

    /**
     * Set category id
     *
     * @param int $categoryId CategoryId
     *
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Set discount factor
     *
     * @param string $discountFactor DiscountFactor
     *
     * @return $this
     */
    public function setDiscountFactor($discountFactor)
    {
        return $this->setData(self::DISCOUNT_FACTOR, $discountFactor);
    }

    /**
     * Set is active
     *
     * @param int $isActive IsActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Set discount type
     *
     * @param int $discountType
     * @return $this
     */
    public function setDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }
}
