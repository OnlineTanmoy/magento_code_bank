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
namespace Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Contact;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Abstract Class AddContactPersonButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AddContactPersonButton extends Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * AddContactPersonButton constructor.
     *
     * @param Context  $context  context
     * @param Registry $registry registry
     * @param array    $data     data
     *                           return
     *                           void
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
     * Get customer create url
     *
     * @return mixed
     */
    public function getCustomerCreateUrl()
    {
        return $this->getUrl('customer/index/new', []);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_contact';
        $this->_blockGroup = 'Appseconnect_ContactPerson';
        
        $this->buttonList->add(
            'addcustomer', [
            'label' => __('Add New Customer'),
            'class' => 'primary',
            'on_click' => 'setLocation("' . $this->getCustomerCreateUrl() . '")'
            ]
        );
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $newsRegistry = $this->coreRegistry->registry('insync_contactperson');
        if ($newsRegistry->getId()) {
            $newsTitle = $this->escapeHtml($newsRegistry->getTitle());
            return __("Edit  '%1'", $newsTitle);
        } else {
            return __('Add');
        }
    }
    
    /**
     * Get save and continue url
     *
     * @return void
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save', [
            '_current' => true,
            'back' => 'edit',
            'active_tab' => '{{tab_id}}'
            ]
        );
    }

    /**
     * Prepare layout
     *
     * @return $this
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
