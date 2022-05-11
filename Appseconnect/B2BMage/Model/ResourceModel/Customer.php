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
 * Class Customer
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_customer_specialprice', 'id');
    }

    /**
     * Filter
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection     Collection
     * @param int                                                     $specialPriceId SpecialPriceId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function filter($collection, $specialPriceId)
    {
        $collection->getSelect()->join(
            ['specialprice_map' => $this->_resources->getTableName('insync_specialprice_map')],
            'specialprice_map.product_sku = e.sku',
            ['parent_id']
        );
        $collection->getSelect()->where("specialprice_map.parent_id = " . $specialPriceId);

        return $collection;
    }

    /**
     * AddProductMapCollection
     *
     * @param int                                                           $productSku ProductSku
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Customer\Collection $collection Collection
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Customer\Collection
     */
    public function addProductMapCollection($productSku, $collection)
    {
        $collection->getSelect()
            ->where("specialprice.product_sku = ?", $productSku)
            ->join(
                ['specialprice' => $this->_resources->getTableName('insync_specialprice_map')],
                'main_table.id = specialprice.parent_id',
                [
                    'special_price',
                    'product_sku'
                ]
            );

        return $collection;
    }
}
