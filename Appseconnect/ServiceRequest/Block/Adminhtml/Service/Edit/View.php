<?php
namespace Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit;

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
        $this->setId('service_request_edit_view');
        $this->setDestElementId('service_request_edit');
        $this->setTitle(__('Service Request View'));
    }
}
