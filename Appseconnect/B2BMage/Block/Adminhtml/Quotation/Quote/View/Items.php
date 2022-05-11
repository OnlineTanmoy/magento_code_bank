<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\View;

use Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\Collection;

/**
 * Class Items
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Items extends \Appseconnect\B2BMage\Block\Adminhtml\Quotation\Items\AbstractItems
{

    const DELETE_URL = 'b2bmage/quotation/index_delete';

    /**
     * GetColumns
     *
     * @return array
     * @since  100.1.0
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _beforeToHtml()
    {
        if (! $this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid parent block for this block')
            );
        }
        $this->setOrder(
            $this->getParentBlock()
                ->getQuote()
        );
        parent::_beforeToHtml();
    }

    /**
     * GetDeletePostJson
     *
     * @param \Appseconnect\Quotation\Model\QuoteProduct $item Item
     *
     * @return string
     */
    public function getDeletePostJson($item)
    {
        $url = $this->getUrl(self::DELETE_URL);
        
        $data = [
            'quote_id' => $this->getQuote()->getId(),
            'item_id' => $item->getId()
        ];
        
        if (! $this->_request->isAjax()) {
            $data[\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = $this->getCurrentBase64Url();
        }
        return json_encode(
            [
            'action' => $url,
            'data' => $data
            ]
        );
    }

    /**
     * GetMinimalQty
     *
     * @param \Magento\Catalog\Model\Product $product Product
     *
     * @return NULL|int
     */
    public function getMinimalQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()
                ->getWebsiteId()
        );
        $minSaleQty = $stockItem->getMinSaleQty();
        return $minSaleQty > 0 ? $minSaleQty : null;
    }

    /**
     * GetProductDefaultQty
     *
     * @param \Magento\Catalog\Model\Product $product Product
     *
     * @return int|bool
     */
    public function getProductDefaultQty($product = null)
    {
        if (! $product) {
            $product = $this->getProduct();
        }
        
        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }
        
        return $qty;
    }

    /**
     * Retrieve quote items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        return $this->getQuote()->getItemsCollection();
    }
}
