<?php
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View;

use Appseconnect\B2BMage\Model\ResourceModel\PriceFactory;

class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var array
     */
    public $assignedProducts = [];

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
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * @var PriceFactory
     */
    public $pricelistResourceFactory;

    /**
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist
     * @param PriceFactory $pricelistResourceFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        PriceFactory $pricelistResourceFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->pricelistResourceFactory = $pricelistResourceFactory;
        $this->productFactory = $productFactory;
        $this->helperPricelist = $helperPricelist;
        $this->moduleManager = $moduleManager;
        $this->catalogConfig = $catalogConfig;
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
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
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
                $this->getCollection()->addFieldToFilter('entity_id', [
                    'in' => $productIds
                ]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', [
                        'nin' => $productIds
                    ]);
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
        $pricelistResourceModel = $this->pricelistResourceFactory->create();
        $pricelistId = $this->getRequest()->getParam('id');
        if ($pricelistId) {
            $this->setDefaultFilter([
                'in_pricelist' => 1
            ]);
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
            $collection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        $collection->addAttributeToSelect('price');
        $collection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute(
            'visibility',
            'catalog_product/visibility',
            'entity_id',
            null,
            'inner'
        );
        
        if (! $reset) {			
			$pricelist=$this->helperPricelist->getPricelistProducts($pricelistId);
			if($pricelist->getSize()>0){			
				$filterdCollection = $pricelistResourceModel->filter($collection, $pricelistId);
				$collection = $filterdCollection;
			}
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
        $this->addColumn('entity_id', [
            'header' => __('ID'),
            'sortable' => true,
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'index' => 'entity_id'
        ]);
        $this->addColumn('name', [
            'header' => __('Product'),
            'index' => 'name'
        ]);
        $this->addColumn('sku', [
            'header' => __('SKU'),
            'index' => 'sku'
        ]);
        
        $this->addColumn('in_products', [
            'header' => __('Select'),
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'index' => 'entity_id',
            'sortable' => false,
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select'
        ]);
        $this->addColumn('final_price', [
            'filter' => false,
            'sortable' => false,
            'header' => __('Price'),
            'renderer' => 'Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\Renderer\Price',
            'name' => 'final_price',
            'inline_css' => 'price',
            'type' => 'input',
            'validate_class' => 'validate-number',
            'index' => 'final_price'
        ]);
        
        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('b2bmage/pricelist/product_loadBlock', [
            'block' => 'search_grid',
            '_current' => true,
            'collapse' => null,
            'reset' => 1
        ]);
    }

    /**
     * Get selected products
     *
     * @return mixed
     */
    public function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products');
        $pricelistId = $this->getRequest()->getParam('id');
        
        if ($products === null) {
            $pricelistProducts = $this->helperPricelist->getPricelistProducts($pricelistId);
            $assignedProducts = [];
            foreach ($pricelistProducts as $item) {
                $assignedProducts[$item->getProductId()] = $item->getFinalPrice();
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
