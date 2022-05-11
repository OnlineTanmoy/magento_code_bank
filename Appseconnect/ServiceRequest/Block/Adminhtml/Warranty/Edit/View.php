<?php
namespace Appseconnect\ServiceRequest\Block\Adminhtml\Warranty\Edit;

use Magento\Backend\Model\Auth\Session;

class View extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('warranty_request_edit_view');
        $this->setDestElementId('warranty_request_edit');
        $this->setTitle(__('Warranty Request View'));
    }
}
