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
 * Class Price
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Price extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_pricelist', 'id');
    }

    /**
     * Filter
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection  Collection
     * @param int                                                     $pricelistId PricelistId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function filter($collection, $pricelistId)
    {
        $collection->getSelect()->join(
            ['pricelist_map' => $this->_resources->getTableName('insync_product_pricelist_map')],
            'pricelist_map.product_id = e.entity_id',
            ['pricelist_id']
        );

        $collection->getSelect()
            ->where("pricelist_map.pricelist_id = " . $pricelistId);

        return $collection;
    }
}
