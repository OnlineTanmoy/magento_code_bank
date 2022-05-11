<?php
namespace Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab\View;


class OrderGrid extends \Magento\Backend\Block\Widget\Grid\Extended
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
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
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
        $this->setId('service_order_search_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }


    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    public function _prepareCollection()
    {

        $serviceId = $this->getRequest()->getParam('id');
        $collection = $this->orderFactory->create()->getCollection();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('service_id', $serviceId);
        $collection->getSelect()->joinLeft(
            'sales_order_status', 'main_table.status=sales_order_status.status', array('label'));
        

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
        $this->addColumn('increment_id', [
            'header' => __('ID'),
            'sortable' => true,
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'index' => 'increment_id'
        ]);
        $this->addColumn('created_at', [
            'header' => __('Request Date'),
            'type'      => 'datetime',
            'index' => 'created_at'
        ]);
        $this->addColumn('label', [
            'header' => __('Status'),
            'index' => 'label'
        ]);

        $this->addColumn('grand_total', [
            'header' => __('Grand Total'),
            'index' => 'grand_total'
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
        return $this->getUrl('servicerequest/listing/order_loadBlock', [
            'block' => 'search_grid',
            '_current' => true,
            'collapse' => null,
            'reset' => 1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $row->getId()]);
    }



}
