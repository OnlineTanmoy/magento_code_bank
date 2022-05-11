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

namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search;

use Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search\Grid\Renderer\Price;
use Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search\Grid\Renderer\Product;
use Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search\Grid\Renderer\Qty;

/**
 * Class Grid
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Sales config
     *
     * @var \Magento\Sales\Model\Config
     */
    public $salesConfig;

    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    public $sessionQuote;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    public $catalogConfig;

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context        Context
     * @param \Magento\Backend\Helper\Data            $backendHelper  BackendHelper
     * @param \Magento\Catalog\Model\ProductFactory   $productFactory ProductFactory
     * @param \Magento\Catalog\Model\Config           $catalogConfig  CatalogConfig
     * @param \Magento\Backend\Model\Session\Quote    $sessionQuote   SessionQuote
     * @param \Magento\Sales\Model\Config             $salesConfig    SalesConfig
     * @param array                                   $data           Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->sessionQuote = $sessionQuote;
        $this->salesConfig = $salesConfig;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('quote_item_search_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->sessionQuote->getStore();
    }

    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * Add column filter to collection
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column Column
     *
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter(
                    'entity_id',
                    [
                        'in' => $productIds
                    ]
                );
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter(
                        'entity_id',
                        [
                            'nin' => $productIds
                        ]
                    );
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    public function _prepareCollection()
    {
        $attributes = $this->catalogConfig->getProductAttributes();
        $collection = $this->productFactory->create()->getCollection();
        $collection->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', $this->salesConfig->getAvailableProductTypes())
            ->addAttributeToSelect('gift_message_available');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'renderer' => Product::class,
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'currency_code' => $this->getStore()
                    ->getCurrentCurrencyCode(),
                'rate' => $this->getStore()
                    ->getBaseCurrency()
                    ->getRate(
                        $this->getStore()
                            ->getCurrentCurrencyCode()
                    ),
                'index' => 'price',
                'renderer' => Price::class
            ]
        );

        $this->addColumn(
            'in_products',
            [
                'header' => __('Select'),
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'sortable' => false,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => Qty::class,
                'name' => 'qty',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'b2bmage/quotation/index_loadBlock',
            [
                'block' => 'search_grid',
                '_current' => true,
                'collapse' => null
            ]
        );
    }

    /**
     * Get selected products
     *
     * @return mixed
     */
    private function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', []);

        return $products;
    }

    /**
     * Add custom options to product collection
     *
     * @return $this
     */
    public function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}
