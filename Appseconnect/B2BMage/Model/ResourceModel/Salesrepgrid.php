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
 * Class Salesrepgrid
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Salesrepgrid extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_salesrep_grid', 'id');
    }

    /**
     * GetSalesRepCustomers
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection Collection
     * @param int                                                       $customerId CustomerId
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getSalesRepCustomers($collection, $customerId)
    {
        $collection->getSelect()
            ->where("customergrid.salesrep_customer_id = ?", $customerId)
            ->join(
                ['customer' => $this->_resources->getTableName('insync_salesrepresentative')],
                'e.entity_id = customer.customer_id',
                ['salesrep_id']
            )
            ->join(
                ['customergrid' => $this->_resources->getTableName('insync_salesrep_grid')],
                'customer.salesrep_id = customergrid.id',
                ['salesrep_customer_id']
            );

        return $collection;
    }
}
