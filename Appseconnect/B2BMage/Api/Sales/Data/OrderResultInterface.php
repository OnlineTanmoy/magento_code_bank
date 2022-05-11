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
namespace Appseconnect\B2BMage\Api\Sales\Data;

/**
 * Interface OrderResultInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface OrderResultInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
* #@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ORDER_ID = 'order_id';

    const INCREMENT_ID = 'increment_id';
    /**
     * #@-
     */

    /**
     * Get Order Id.
     *
     * @return string|null
     */
    public function getOrderId();
    
    /**
     * Set Order Id.
     *
     * @param string $orderId order id
     *
     * @return $this
     */
    public function setOrderId($orderId = null);
    
    /**
     * Get Increment Id.
     *
     * @return string|null
     */
    public function getIncrementId();
    
    /**
     * Set Increment Id.
     *
     * @param string $incrementId increment id
     *
     * @return $this
     */
    public function setIncrementId($incrementId = null);
}
