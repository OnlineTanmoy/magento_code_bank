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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerTierPrice;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Abstract Class Edit
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Edit constructor.
     *
     * @param Context  $context  context
     * @param Registry $registry registry
     * @param array    $data     data
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
        $this->_controller = 'adminhtml_customerTierPrice';
        $this->_blockGroup = 'Appseconnect_B2BMage';
        
        parent::_construct();
        
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue', [
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
            ], - 100
        );
        
        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $newsRegistry = $this->coreRegistry->registry('Appseconnect_CustomerTierprice');
        if ($newsRegistry->getId()) {
            $newsTitle = $this->escapeHtml($newsRegistry->getTitle());
            return __("Edit  '%1'", $newsTitle);
        } else {
            return __('Add');
        }
    }

    /**
     * Get save an continue url
     *
     * @return mixed
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'b2bmage/*/save', [
            '_current' => true,
            'back' => 'edit',
            'active_tab' => '{{tab_id}}'
            ]
        );
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
