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

use Magento\Framework\Api\AbstractExtensibleObject;
use Appseconnect\B2BMage\Api\Pricelist\Data\PricelistAssignInterface;

/**
 * Class PricelistAssign
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class PricelistAssign extends AbstractExtensibleObject implements PricelistAssignInterface
{
    /**
     * Set Customer Id
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
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }
    
    /**
     * Set Pricelist Id
     *
     * @param int $pricelistId PricelistId
     *
     * @return $this
     */
    public function setPricelistId($pricelistId)
    {
        return $this->setData(self::PRICELIST_ID, $pricelistId);
    }
    
    /**
     * Get Pricelist Id
     *
     * @return int|null
     */
    public function getPricelistId()
    {
        return $this->_get(self::PRICELIST_ID);
    }
}
