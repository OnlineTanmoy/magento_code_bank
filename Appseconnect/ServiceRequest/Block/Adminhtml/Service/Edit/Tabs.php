<?php
namespace Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Service Request'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $serviceRequestId = $this->getRequest()->getParam('id');
        if ($serviceRequestId) {
            $this->addTab('order', [
                'label' => __('Service Order'),
                'title' => __('Service Order'),
                'content' => $this->getLayout()
                    ->createBlock('Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab\View\OrderGrid')
                    ->toHtml()
            ]);
            $this->addTab('invoice', [
                'label' => __('Service Invoice'),
                'title' => __('Service Invoice'),
                'content' => $this->getLayout()
                    ->createBlock('Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab\View\InvoiceGrid')
                    ->toHtml()
            ]);

            $this->_updateActiveTab();
        }
        return parent::_beforeToHtml();
    }

    /**
     * @return void
     */
    public function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
