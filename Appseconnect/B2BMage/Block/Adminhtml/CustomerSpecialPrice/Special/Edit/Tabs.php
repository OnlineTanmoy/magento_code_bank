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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Special\Edit;

/**
 * Abstract Class Tabs
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * Construct methos
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Customer Special Price Information'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        if ($this->getRequest()->getParam('id')) {
            $this->addTab(
                'product_tab', [
                'label' => __('Products'),
                'title' => __('Products'),
                'content' => $this->getLayout()
                    ->createBlock('Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View\GridInit')
                    ->toHtml()
                ]
            );
            
            $this->_updateActiveTab();
        }
        return parent::_beforeToHtml();
    }

    /**
     * Update active tab
     *
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
