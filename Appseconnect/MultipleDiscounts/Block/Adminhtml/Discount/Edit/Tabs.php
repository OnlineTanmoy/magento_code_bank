<?php

namespace Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount\Edit;

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
        $this->setTitle(__('Discount Information'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        if ($this->getRequest()->getParam('id')) {
            $this->addTab('customer_tab', [
                'label' => __('Customers'),
                'title' => __('Customers'),
                'content' => $this->getLayout()
                    ->createBlock('Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount\Edit\Tab\View\Grid')
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