<?php

namespace Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount\Edit\Tab\View;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    public $moduleManager;

    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    public $setsFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    public $websiteFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * @var \Appseconnect\MultipleDiscounts\Helper\Data
     */
    public $helper;

    public $storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resources
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Appseconnect\MultipleDiscounts\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resources,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Appseconnect\MultipleDiscounts\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->setsFactory = $setsFactory;
        $this->customerFactory = $customerFactory;
        $this->resources = $resources;
        $this->moduleManager = $moduleManager;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @ERROR!!!
     */
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer_id');
        $this->getMassactionBlock()->setTemplate('Appseconnect_MultipleDiscounts::widget/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->addItem('assign', [
            'label' => __('Assign'),
            'url' => $this->getUrl('multiplediscount/price/assigncustomers', [
                '_current' => true,
                'parent_id' => $this->getRequest()->getParam('id')
            ]),
            'confirm' => __('Are you sure you want to assign?')
        ]);

        $this->getMassactionBlock()->addItem('unassign', [
            'label' => __('Unassign'),
            'url' => $this->getUrl('multiplediscount/price/unassigncustomers', [
                '_current' => true,
                'parent_id' => $this->getRequest()->getParam('id')
            ]),
            'confirm' => __('Are you sure you want to unassign?')
        ]);
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function _prepareCollection()
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $reset = $this->getRequest()->getParam('reset');
        $assignedCustomerId = $this->helper->getCustomerId($this->getRequest()->getParam('id'), $reset);
        $collection = $this->customerFactory->create()->getCollection();

        if (!$reset && !empty($assignedCustomerId)) {
            $collection->addFieldToFilter('entity_id', [
                'in' => $assignedCustomerId
            ]);
        }
        $collection->addFieldToFilter('website_id', $websiteId);
        $collection->addExpressionAttributeToSelect('name', '(CONCAT({{firstname}},"  ",{{lastname}}))', [
            'firstname',
            'lastname'
        ])
            ->addFieldToFilter('customer_type', 4)
            ->addFieldToFilter('customer_status', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareColumns()
    {
        $this->addColumn('customer_entity_id', [
            'header' => __('ID'),
            'width' => '5px',
            'type' => 'number',
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('customer_name', [
            'header' => __('Name'),
            'type' => 'text',
            'index' => 'name',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('customer_email', [
            'header' => __('Email'),
            'type' => 'text',
            'index' => 'email',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('customer_website', [
            'header' => __('Website'),
            'type' => 'options',
            'index' => 'website_id',
            'options' => $this->helper->getWebsite(),
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('assign_status', [
            'header' => __('Status'),
            'type' => 'text',
            'index' => 'customer_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'renderer' => 'Appseconnect\MultipleDiscounts\Block\Adminhtml\Renderer\Status'
        ]);

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

    /**
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('multiplediscount/price/customerlayout', [
            '_current' => true,
            'reset' => 1,
            'parent_id' => $this->getRequest()->getParam('id')
        ]);
    }
}