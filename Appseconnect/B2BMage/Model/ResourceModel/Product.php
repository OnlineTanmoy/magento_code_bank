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
 * Class Product
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_customer_tierprice', 'id');
    }

    /**
     * GetAssignedProducts
     *
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Product\Collection $collection Collection
     * @param mixed                                                        $productSku ProductSku
     * @param int                                                          $qtyItem    QtyItem
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Product\Collection
     */
    public function getAssignedProducts($collection, $productSku, $qtyItem)
    {
        $collection->getSelect()
            ->where("map.product_sku = ?", $productSku)
            ->where("map.quantity <= ?", $qtyItem)
            ->order('map.quantity  DESC')
            ->join(
                ['map' => $this->_resources->getTableName('insync_tierprice_map')],
                'main_table.id = map.parent_id',
                [
                    'parent_id' => 'parent_id',
                    'quantity' => 'quantity',
                    'tier_price' => 'tier_price'
                ]
            )
            ->limit(1);
        return $collection;
    }
}
