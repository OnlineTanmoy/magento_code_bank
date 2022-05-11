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
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit;

/**
 * Class Tabs
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
        $this->setTitle(__('Pricelist Price Information'));
    }

    /**
     * BeforeToHtml
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        $pricelistId = $this->getRequest()->getParam('id');
        if ($pricelistId) {
            $this->addTab(
                'products',
                [
                'label' => __('Products'),
                'title' => __('Products'),
                'content' => $this->getLayout()
                    ->createBlock('Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\GridInit')
                    ->toHtml()
                ]
            );
            
            $this->_updateActiveTab();
        }
        return parent::_beforeToHtml();
    }

    /**
     * UpdateActiveTab
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
