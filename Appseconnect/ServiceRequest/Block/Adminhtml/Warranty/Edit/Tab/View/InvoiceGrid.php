<?php
namespace Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab\View;


class InvoiceGrid extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * InvoiceGrid constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
        $this->helperPricelist = $helperPricelist;
        $this->moduleManager = $moduleManager;
        $this->catalogConfig = $catalogConfig;
        $this->_coreRegistry = $coreRegistry;
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
        $serviceModel = $this->_coreRegistry->registry('insync_servicerequest');
        $orderDetails = $this->orderFactory->create()->load($serviceModel->getData('order_id'));
        $invoiceCollection = $orderDetails->getInvoiceCollection();
        $invoiceCollection->getSelect()->joinLeft(
            array("sorder" => "sales_order"),
            'main_table.order_id = sorder.entity_id',
            ['order_increment_id' => 'sorder.increment_id', 'order_date' => 'sorder.created_at', 'bill_to_name' => 'concat(sorder.customer_firstname, " " , sorder.customer_lastname)']
        )->joinLeft(
            array("sorderp" => "sales_order_payment"),
            'main_table.order_id = sorderp.parent_id',
            ['order_payment_method' => 'sorderp.method']
        );
        $this->setCollection($invoiceCollection);
        return parent::_prepareCollection();
    }

    /**
     * Get Invoice id of the selected Invoice
     *
     * @param $row
     * @return mixed
     */
    private function _getInvoiceId($row) {
        $orderDetails = $this->orderFactory->create()->load($row->getData('order_id'));
        $invoiceCollection = $orderDetails->getInvoiceCollection();
        $invoiceData = $invoiceCollection->getFirstItem();
        return $invoiceData->getId();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    public function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header' => __('Invoice'),
            'sortable' => true,
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'index' => 'increment_id'
        ]);
        $this->addColumn('created_at', [
            'header' => __('Invoice Date'),
            'type'      => 'datetime',
            'index' => 'created_at'
        ]);
        $this->addColumn('order_increment_id', [
            'header' => __('Order #'),
            'index' => 'order_increment_id'
        ]);
        $this->addColumn('order_date', [
            'header' => __('Order Date'),
            'type'      => 'datetime',
            'index' => 'order_date'
        ]);
        $this->addColumn('bill_to_name', [
            'header' => __('Customer Name'),
            'index' => 'bill_to_name'
        ]);
        $this->addColumn('order_payment_method', [
            'header' => __('Payment Method'),
            'index' => 'order_payment_method'
        ]);
        $this->addColumn('state', [
            'header' => __('Status'),
            'index' => 'state',
            'type'=>'options',
            'options' => array(1 => 'Open', 2 => 'Paid', 3 => 'Canceled')
        ]);
        $this->addColumn('grand_total', [
            'header' => __('Amount'),
            'index' => 'grand_total'
        ]);
        $this->addColumn('sap_invoice_filename', [
            'header' => __('Invoice file'),
            'index' => 'sap_invoice_filename'
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
        return $this->getUrl('sales/order_invoice/view', ['invoice_id' => $this->_getInvoiceId($row)]);
    }
}
