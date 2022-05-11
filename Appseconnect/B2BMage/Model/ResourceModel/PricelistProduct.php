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

use Magento\Framework\Exception\NotFoundException;

/**
 * Class PricelistProduct
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class PricelistProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_product_pricelist_map', 'product_pricelist_map_id');
    }

    /**
     * RemoveMapping
     *
     * @param int $id Id
     *
     * @return boolean
     * @throws NotFoundException
     */
    public function removeMapping($id)
    {
        $connection = $this->getConnection();
        try {
            $connection->delete(
                $this->_resources->getTableName('insync_product_pricelist_map'),
                ['pricelist_id = ?' => $id]
            );
        } catch (\Exception $e) {
            throw new NotFoundException(
                __('Something went wrong while mapping the products.')
            );
        }
        return true;
    }
}
