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

use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;

/**
 * Class Approver
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Approver extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_approver', 'insync_approver_id');
    }

    /**
     * GetContacts
     *
     * @param int                $customerId         CustomerId
     * @param CustomerCollection $customerCollection CustomerCollection
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getContacts($customerId, $customerCollection)
    {
        $customerCollection->getSelect()
            ->where("customer.customer_id = ?", $customerId)
            ->join(
                ['customer' => $this->_resources->getTableName('insync_contactperson')],
                'customer.contactperson_id = e.entity_id',
                ['contactperson_id']
            );
        return $customerCollection;
    }
}
