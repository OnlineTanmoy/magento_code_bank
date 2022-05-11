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

namespace Appseconnect\B2BMage\Model\ResourceModel;

/**
 * Class Salesrep
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Salesrep extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_salesrepresentative', 'id');
    }

    /**
     * GetJoinSalesRepData
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection Collection
     * @param int                                                       $result     Result
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getJoinSalesRepData($collection, $result)
    {
        $collection->getSelect()
            ->join(
                ['customer' => $this->_resources->getTableName('insync_salesrepresentative')],
                'e.entity_id = customer.customer_id',
                ['customer.salesrep_id']
            )
            ->where("salesrep_grid.salesrep_customer_id	 = ?", $result)
            ->join(
                ['salesrep_grid' => $this->_resources->getTableName('insync_salesrep_grid')],
                'customer.salesrep_id = salesrep_grid.id',
                ['salesrep_grid.salesrep_customer_id']
            );
        return $collection;
    }
}
