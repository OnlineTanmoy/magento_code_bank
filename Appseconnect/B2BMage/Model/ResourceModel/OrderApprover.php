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
 * Class OrderApprover
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderApprover extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_order_approval', 'id');
    }

    /**
     * GetApprovalOrders
     *
     * @param int                                                 $customerId CustomerId
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orders     Orders
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getApprovalOrders($customerId, $orders)
    {
        $orders->getSelect()
            ->where("order_approval.contact_person_id = ?", $customerId)
            ->join(
                ['order_approval' => $this->_resources->getTableName('insync_order_approval')],
                'order_approval.increment_id = main_table.increment_id',
                ['approval_id' => 'id']
            );
        $orders->join(
            ['customer' => $this->_resources->getTableName('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['customer_email' => 'email']
        );

        return $orders;
    }
}
