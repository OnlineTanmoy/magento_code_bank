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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View;

use Appseconnect\B2BMage\Model\ResourceModel\CustomerFactory;

/**
 * Abstract Class ProductGrid
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Sales config
     *
     * @var \Magento\Sales\Model\Config
     */
    public $salesConfig;

    /**
     * Assign product
     *
     * @var array
     */
    public $assignedProducts = [];

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
     * Spcial price resource
     *
     * @var CustomerFactory
     */
    public $specialPriceResourceFactory;

    /**
     * Customer special price helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data
     */
    public $helperCustomerSpecialPrice;

    /**
     * Module manager
     *
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * ProductGrid constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice  helpwr customer special price
     * @param CustomerFactory                                        $specialPriceResourceFactory special price resource
     * @param \Magento\Backend\Block\Template\Context                $context                     context
     * @param \Magento\Backend\Helper\Data                           $backendHelper               backend helper
     * @param \Magento\Catalog\Model\ProductFactory                  $productFactory              product
     * @param \Magento\Catalog\Model\Config                          $catalogConfig               catalog config
     * @param \Magento\Backend\Model\Session\Quote                   $sessionQuote                session quote
     * @param \Magento\Sales\Model\Config                            $salesConfig                 sales config
     * @param \Magento\Framework\Module\Manager                      $moduleManager               module manager
     * @param array                                                  $data                        data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        CustomerFactory $specialPriceResourceFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->specialPriceResourceFactory = $specialPriceResourceFactory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->moduleManager = $moduleManager;
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
        $this->setId('pricelist_product_search_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Add column filter to collection
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column column
     *
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter(
                    'entity_id', [
                    'in' => $productIds
                    ]
                );
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter(
                        'entity_id', [
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
        $specialPriceResourceModel = $this->specialPriceResourceFactory->create();
        $specialPriceId = $this->getRequest()->getParam('id');
        if ($specialPriceId) {
            $this->setDefaultFilter(
                [
                'in_specialprice' => 1,
                    'in_products' => 1
                ]
            );
        }
        $reset = $this->getRequest()->getParam('reset');
        $attributes = $this->catalogConfig->getProductAttributes();
        
        $collection = $this->productFactory->create()->getCollection();
        $collection->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', 'simple')
            ->addAttributeToSelect('gift_message_available');
        
        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection
            ->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        $collection->addAttributeToSelect('price');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        
        if (! $reset) {
            $filterdCollection = $specialPriceResourceModel->filter(
                $collection,
                $specialPriceId
            );
            $collection = $filterdCollection;
        }
        
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
            'entity_id', [
            'header' => __('ID'),
            'sortable' => true,
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name', [
            'header' => __('Product'),
            'index' => 'name'
            ]
        );
        $this->addColumn(
            'sku', [
            'header' => __('SKU'),
            'index' => 'sku'
            ]
        );
        
        $this->addColumn(
            'in_products', [
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
            'special_price', [
            'filter' => false,
            'sortable' => false,
            'header' => __('Special Price'),
            'renderer' => 'Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View\Renderer\Price',
            'name' => 'special_price',
            'inline_css' => 'price',
            'type' => 'input',
            'validate_class' => 'validate-number',
            'index' => 'special_price'
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
            'b2bmage/special/product_loadBlock', [
            'block' => 'search_grid',
            '_current' => true,
            'collapse' => null,
            'reset' => 1
            ]
        );
    }

    /**
     * Get selected products
     *
     * @return mixed
     */
    public function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products');
        $specialPriceId = $this->getRequest()->getParam('id');
        
        if ($products === null) {
            $specialPriceProducts = $this->helperCustomerSpecialPrice->getSpecialPriceProducts(
                $specialPriceId
            );
            $assignedProducts = [];
            foreach ($specialPriceProducts as $item) {
                $assignedProducts[$item->getProductId()] = $item->getSpecialPrice();
            }
            $products = $assignedProducts;
            return array_keys($products);
        }
        
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
