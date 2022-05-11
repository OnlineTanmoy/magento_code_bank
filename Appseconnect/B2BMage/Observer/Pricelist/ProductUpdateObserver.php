<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\Pricelist;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductUpdateObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductUpdateObserver implements ObserverInterface
{

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * ProductUpdateObserver constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resources Resources
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resources
    ) {
        $this->resources = $resources;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productData = $observer->getEvent()->getData('product');
        $productId = $productData->getId();
        $productPrice = $productData->getPrice();
        $pricelistTable = $this->resources->getTableName('insync_product_pricelist_map');
        $connection = $this->resources->getConnection();

        $productData = [];
        $productData['original_price'] = $productPrice;
        $where = [];
        $where['product_id=?'] = $productId;
        $connection->update($pricelistTable, $productData, $where);
    }
}
