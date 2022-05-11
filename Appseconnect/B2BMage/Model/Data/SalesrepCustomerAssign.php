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
 * Class SalesrepCustomerAssign
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SalesrepCustomerAssign extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface
{
    /**
     * Get salesrep id
     *
     * @return int|null
     */
    public function getSalesrepId()
    {
        return $this->_get(self::SALESREP_ID);
    }

    /**
     * Get customer ids
     *
     * @return string[]|null
     */
    public function getCustomerIds()
    {
        return $this->_get(self::CUSTOMER_IDS);
    }

    /**
     * Set salesrep id
     *
     * @param int $salesrepId SalesrepId
     *
     * @return $this
     */
    public function setSalesrepId($salesrepId)
    {
        return $this->setData(self::SALESREP_ID, $salesrepId);
    }

    /**
     * Set customer ids
     *
     * @param string[] $customerIds CustomerIds
     *
     * @return $this
     */
    public function setCustomerIds(array $customerIds)
    {
        return $this->setData(self::CUSTOMER_IDS, $customerIds);
    }
}
