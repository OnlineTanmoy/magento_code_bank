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

namespace Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Customer;

use Magento\Store\Model\Store;

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
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    public $moduleManager;

    /**
     * CollectionFactory
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    public $setsFactory;

    /**
     * WebsiteFactory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    public $websiteFactory;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helper;

    /**
     * Int
     *
     * @var int
     */
    public $salesrepId = null;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                 $context         Context
     * @param \Magento\Framework\App\ResourceConnection                               $resources       Resources
     * @param \Magento\Backend\Helper\Data                                            $backendHelper   BackendHelper
     * @param \Magento\Store\Model\WebsiteFactory                                     $websiteFactory  WebsiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory     SetsFactory
     * @param \Magento\Customer\Model\CustomerFactory                                 $customerFactory CustomerFactory
     * @param \Magento\Framework\Module\Manager                                       $moduleManager   ModuleManager
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data                              $helper          Helper
     * @param array                                                                   $data            Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resources,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helper,
        array $data = []
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->setsFactory = $setsFactory;
        $this->customerFactory = $customerFactory;
        $this->resources = $resources;
        $this->moduleManager = $moduleManager;
        $this->helper = $helper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
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
     * PrepareMassaction
     *
     * @return $this
     */
    public function _prepareMassaction()
    {
        $selsrepData = $this->helper->isSalesrep(
            $this->getRequest()
                ->getParam('id'),
            true
        );
        $this->salesrepId = $selsrepData[0]['id'];
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer_id');
        $this->getMassactionBlock()->addItem(
            'assign',
            [
                'label' => __('Assign'),
                'url' => $this->getUrl(
                    'b2bmage/salesrep/assignsalesrep',
                    [
                        '_current' => true,
                        'salesrep_id' => $this->salesrepId
                    ]
                ),
                'confirm' => __('Are you sure you want to assign?')
            ]
        );

        $this->getMassactionBlock()->addItem(
            'unassign',
            [
                'label' => __('Unassign'),
                'url' => $this->getUrl(
                    'b2bmage/salesrep/unassignsalesrep',
                    [
                        '_current' => true,
                        'salesrep_id' => $this->salesrepId
                    ]
                ),
                'confirm' => __('Are you sure you want to unassign?')
            ]
        );
        return $this;
    }

    /**
     * PrepareCollection
     *
     * @return $this
     */
    public function _prepareCollection()
    {
        $selsrepData = $this->helper->isSalesrep(
            $this->getRequest()
                ->getParam('id'),
            true
        );

        $reset = $this->getRequest()->getParam('reset');
        $assignedCustomerId = $this->helper->getCustomerId($this->salesrepId, $reset);
        $collection = $this->customerFactory->create()->getCollection();

        if (!$reset && !empty($assignedCustomerId)) {
            $collection->addFieldToFilter(
                'entity_id',
                [
                    'in' => $assignedCustomerId
                ]
            );
        }
        $collection->addFieldToFilter('website_id', $selsrepData[0]['website_id']);
        $collection->addExpressionAttributeToSelect(
            'name',
            '(CONCAT({{firstname}},"  ",{{lastname}}))',
            [
                'firstname',
                'lastname'
            ]
        )->addFieldToFilter('customer_type', 4);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * PrepareColumns
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'customer_entity_id',
            [
                'header' => __('ID'),
                'width' => '5px',
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'customer_email',
            [
                'header' => __('Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'customer_website',
            [
                'header' => __('Website'),
                'type' => 'options',
                'index' => 'website_id',
                'options' => $this->helper->getWebsite(),
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'assign_status',
            [
                'header' => __('Status'),
                'type' => 'text',
                'index' => 'customer_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Renderer\Status'
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

    /**
     * GetGridUrl
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'b2bmage/salesrep/customerlayout',
            [
                '_current' => true,
                'reset' => 1,
                'salesrep_id' => $this->salesrepId
            ]
        );
    }
}
