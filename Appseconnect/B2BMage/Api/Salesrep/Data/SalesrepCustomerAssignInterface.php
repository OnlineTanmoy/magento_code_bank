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
namespace Appseconnect\B2BMage\Api\Salesrep\Data;

/**
 * Interface SalesrepCustomerAssignInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface SalesrepCustomerAssignInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
* #@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const SALESREP_ID = 'salesrep_id';
    const CUSTOMER_IDS = 'customer_ids';
    /**
     * #@-
     */

    /**
     * Get salesrep id
     *
     * @return int|null
     */
    public function getSalesrepId();

    /**
     * Set salesrep id
     *
     * @param int $salesrepId salesrep id
     *
     * @return $this
     */
    public function setSalesrepId($salesrepId);
    /**
     * Get customer ids
     *
     * @return string[]|null
     */
    public function getCustomerIds();

    /**
     * Set customer ids
     *
     * @param string[] $customerIds customer ids
     *
     * @return $this
     */
    public function setCustomerIds(array $customerIds);
}
