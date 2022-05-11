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

namespace Appseconnect\B2BMage\Model\ResourceModel\Price;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * String
     *
     * @var string
     */
    public $idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\B2BMage\Model\Price',
            'Appseconnect\B2BMage\Model\ResourceModel\Price'
        );
        $this->_map['fields']['id'] = 'main_table.id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * GetPricelistProduct
     *
     * @param int $customerPricelistCode CustomerPricelistCode
     * @param int $productId             ProductId
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Price\Collection
     */
    public function getPricelistProduct($customerPricelistCode, $productId)
    {
        $this->getConnection();
        $this->getSelect()
            ->join(
                ['pricelistmap' => $this->getTable('insync_product_pricelist_map')],
                'main_table.id = pricelistmap.pricelist_id'
            )
            ->where('pricelistmap.pricelist_id=?', $customerPricelistCode)
            ->where('pricelistmap.product_id=?', $productId);
        return $this;
    }
}
