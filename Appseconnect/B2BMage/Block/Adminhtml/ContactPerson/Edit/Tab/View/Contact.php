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

namespace Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Edit\Tab\View;

use Appseconnect\B2BMage\Model\ResourceModel\ContactFactory;
use Magento\Store\Model\Store;

/**
 * Abstract Class Contact
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Contact extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * Contact resource
     *
     * @var ContactFactory
     */
    public $contactResourceFactory;

    /**
     * Contact
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Contact constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                 $context                context
     * @param ContactFactory                                                          $contactResourceFactory contact resource
     * @param \Magento\Backend\Helper\Data                                            $backendHelper          backend
     * @param \Magento\Store\Model\WebsiteFactory                                     $websiteFactory         website
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory            sets
     * @param \Magento\Catalog\Model\ProductFactory                                   $productFactory         product
     * @param \Appseconnect\B2BMage\Model\ContactFactory                              $contactFactory         contact
     * @param \Magento\Catalog\Model\Product\Type                                     $type                   type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status                  $status                 status
     * @param \Magento\Catalog\Model\Product\Visibility                               $visibility             visibility
     * @param \Magento\Framework\Module\Manager                                       $moduleManager          module
     * @param array                                                                   $data                   data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ContactFactory $contactResourceFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->setsFactory = $setsFactory;
        $this->productFactory = $productFactory;
        $this->contactResourceFactory = $contactResourceFactory;
        $this->contactFactory = $contactFactory;
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contact construct
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     *  Prepare grid
     *
     * @return void
     */
    public function _prepareGrid()
    {
        $this->setId('comparedproduct_view_compared_grid' . $this->getCustomerId());
        parent::_prepareGrid();
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    public function _prepareCollection()
    {
        $page = ($this->getRequest()->getParam('page')) ? $this->getRequest()->getParam('page') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 20;

        $contactResourceModel = $this->contactResourceFactory->create();
        $collection = $this->contactFactory->create()->getCollection();
        $collection = $contactResourceModel->getContactCollection($collection);
        $collection->addFieldToFilter(
            'customer_id',
            $pricelist_id = $this->getRequest()
                ->getParam('id')
        );
        $this->setCollection($collection);

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return parent::_prepareCollection();
    }

    /**
     * Prepare column
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'width' => '15',
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'type' => 'text',
                'index' => 'firstname',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'width' => 15,
                'type' => 'text',
                'index' => 'lastname',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'contact_email',
            [
                'header' => __('Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'action_edit1',
            [
                'header' => __('Action'),
                'index' => 'image',
                'filter' => false,
                'renderer' => 'Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Renderer\Action'
            ]
        );

        return parent::_prepareColumns();
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
