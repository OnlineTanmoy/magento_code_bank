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

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class OrderResult
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderResult extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Sales\Data\OrderResultInterface
{

    /**
     * Get Order Id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }
    /**
     * Set Order Id
     *
     * @param string $orderId OrderId
     *
     * @return $this
     */
    public function setOrderId($orderId = null)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }
    
    /**
     * Get Increment Id.
     *
     * @return string|null
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
    }
    
    /**
     * Set Increment Id.
     *
     * @param string $incrementId IncrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId = null)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }
}
