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
namespace Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Abstract Class SaveButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Customert account
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * Save button constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context                   context
     * @param \Magento\Framework\Registry           $registry                  registry
     * @param AccountManagementInterface            $customerAccountManagement customer account
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        AccountManagementInterface $customerAccountManagement
    ) {
        parent::__construct($context, $registry);
        $this->customerAccountManagement = $customerAccountManagement;
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $canModify = ! $customerId || ! $this->customerAccountManagement->isReadonly($this->getCustomerId());
        $data = [];
        if ($canModify) {
            $data = [
                'label' => __('Save Contact Person'),
                'class' => 'primary',
                'on_click' => 'setLocation("' . $this->getCustomerSaveUrl() . '")'
            ];
        }
        return $data;
    }

    /**
     * Get customer save data
     *
     * @return string
     */
    public function getCustomerSaveUrl()
    {
        return $this->getUrl('b2bmage/contact/save', []);
    }
}
