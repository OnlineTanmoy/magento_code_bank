<?php
namespace Appseconnect\CompanyDivision\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Appseconnect\CompanyDivision\Model\ResourceModel\Division\CollectionFactory;

class Division extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected
    function _construct()
    {
        parent::_construct();
        $this->setId('view_division_grid');
        $this->setDefaultSort('id', 'desc');
        $this->setSortable(true);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()->addFieldToFilter('customer_id', $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            ['header' => __('ID'), 'index' => 'id', 'type' => 'number', 'width' => '100px']
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Division Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Division Email'),
                'index' => 'email',
            ]
        );

        return parent::_prepareColumns();
    }

    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('customer/index/edit', ['id' => $row->getDivisionId()]);
    }
}
