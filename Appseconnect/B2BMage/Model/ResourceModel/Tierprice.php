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
 * Class Tierprice
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Tierprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_tierprice_map', 'id');
    }

    /**
     * RemoveMapping
     *
     * @param int $id Id
     *
     * @return boolean
     */
    public function removeMapping($id)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->_resources->getTableName('insync_tierprice_map'),
            ['parent_id = ?' => $id]
        );
        return true;
    }
}
