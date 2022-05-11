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
 * Class CategoryDiscount
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CategoryDiscount extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountInterface
{

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Get category discount data
     *
     * @return \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface[]|null
     */
    public function getCategorydiscountData()
    {
        return $this->_get(self::CATEGORYDISCOUNT_DATA);
    }

    /**
     * Set customer id
     *
     * @param int $customerId CustomerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set category discount data
     *
     * @param \Appseconnect\B2BMage\Api\CategoryDiscount\Data\CategoryDiscountDataInterface[] $categorydiscountData CategorydiscountData
     *
     * @return $this
     */
    public function setCategorydiscountData(array $categorydiscountData)
    {
        return $this->setData(self::CATEGORYDISCOUNT_DATA, $categorydiscountData);
    }
}
