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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerTierPrice\Edit;

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
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Tier Price Information'));
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
                'label' => __('Product Details'),
                'url' => $this->getUrl(
                    '*/*/productgrid', [
                    '_current' => true
                    ]
                ),
                'class' => 'ajax'
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
