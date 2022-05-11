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
namespace Appseconnect\B2BMage\Block\Adminhtml\CreditLimit\Edit\Tab\View;

use Magento\Store\Model\Store;
use Magento\Sales\Model\OrderFactory;

/**
 * Abstract Class CreditTransaction
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreditTransaction extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    public $moduleManager;

    /**
     * Sets
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    public $setsFactory;

    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Type
     *
     * @var \Magento\Catalog\Model\Product\Type
     */
    public $type;

    /**
     * Status
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    public $status;

    /**
     * Visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    public $visibility;

    /**
     * Website
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    public $websiteFactory;

    /**
     * Order
     *
     * @var OrderFactory
     */
    public $orderFactory;

    /**
     * Credit
     *
     * @var \Appseconnect\B2BMage\Model\CreditFactory
     */
    public $creditFactory;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CreditTransaction constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                 $context         context
     * @param OrderFactory                                                            $orderFactory    order
     * @param \Magento\Backend\Helper\Data                                            $backendHelper   backend helper
     * @param \Magento\Store\Model\WebsiteFactory                                     $websiteFactory  website
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory     sets
     * @param \Magento\Catalog\Model\ProductFactory                                   $productFactory  product
     * @param \Appseconnect\B2BMage\Model\CreditFactory                               $creditFactory   credit
     * @param \Magento\Catalog\Model\Product\Type                                     $type            type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status                  $status          status
     * @param \Magento\Catalog\Model\Product\Visibility                               $visibility      visibility
     * @param \Magento\Customer\Model\CustomerFactory                                 $customerFactory customer
     * @param \Magento\Framework\Module\Manager                                       $moduleManager   module management
     * @param array                                                                   $data            data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        OrderFactory $orderFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Model\CreditFactory $creditFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->orderFactory = $orderFactory;
        $this->setsFactory = $setsFactory;
        $this->productFactory = $productFactory;
        $this->creditFactory = $creditFactory;
        $this->customerFactory = $customerFactory;
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contruct
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid
     *
     * @return $this
     */
    public function _prepareGrid()
    {
        $this->setId('comparedproduct_view_compared_grid' . $this->getCustomerId());
        parent::_prepareGrid();
    }
    
    /**
     * Prepare page
     *
     * @return void
     */
    public function _preparePage()
    {
        $this->getCollection()
            ->setPageSize(20)
            ->setCurPage(1);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    public function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id');
        $collection = $this->creditFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare colums
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'id', [
            'header' => __('ID'),
            'width' => '15',
            'type' => 'number',
            'index' => 'id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'increment_id', [
            'header' => __('Increment ID'),
            'width' => 15,
            'type' => 'text',
            'index' => 'increment_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'debit_amount', [
            'header' => __('Debit Amount'),
            'width' => 15,
            'type' => 'text',
            'index' => 'debit_amount',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'credit_amount', [
            'header' => __('Credit Amount'),
            'width' => 15,
            'type' => 'text',
            'index' => 'credit_amount',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'available_balance', [
            'header' => __('Available Balance'),
            'width' => 15,
            'type' => 'text',
            'index' => 'available_balance',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'credit_limit', [
            'header' => __('Credit Limit'),
            'type' => 'text',
            'index' => 'credit_limit',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
        );
        
        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Appseconnect\B2BMage\Model\CreditFactory|\Magento\Framework\DataObject $row row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        if ($row->getIncrementId()) {
            $order = $this->orderFactory->create()->loadByIncrementId($row->getIncrementId());
            return $this->getUrl(
                'sales/order/view', [
                'order_id' => $order->getId(),
                '_current' => false
                ]
            );
        }
        return false;
    }

    /**
     * Get headers visibility
     *
     * @return bool @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }
}
