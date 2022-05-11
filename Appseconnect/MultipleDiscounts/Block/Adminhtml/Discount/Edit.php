<?php

namespace Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_discount';
        $this->_blockGroup = 'Appseconnect_MultipleDiscounts';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add('saveandcontinue', [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'saveAndContinueEdit',
                        'target' => '#edit_form'
                    ]
                ]
            ]
        ], - 100);

        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * Retrieve text for header element depending on loaded discount
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('insync_multiplediscount')->getId()) {
            return __("Edit Discount '%1'", $this->escapeHtml($this->coreRegistry->registry('insync_multiplediscount')->getTitle()));
        } else {
            return __('Add Discount');
        }
    }

    /**
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl('multiplediscount/price/save', [
            '_current' => true,
            'back' => 'edit',
            'active_tab' => '{{tab_id}}'
        ]);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";

        return parent::_prepareLayout();
    }
}