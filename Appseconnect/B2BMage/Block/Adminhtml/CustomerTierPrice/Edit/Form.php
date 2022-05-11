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
 * Abstract Class Form
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * Prepare form
     *
     * @return $this
     */
    public function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
