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

namespace Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
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
     * AccountManagementInterface
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * SaveButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context                   Context
     * @param \Magento\Framework\Registry           $registry                  Registry
     * @param AccountManagementInterface            $customerAccountManagement CustomerAccountManagement
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
     * GetButtonData
     *
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $canModify = !$customerId || !$this->customerAccountManagement->isReadonly($this->getCustomerId());
        $data = [];
        if ($canModify) {
            $data = [
                'label' => __('Save Sales Representative'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'save'
                        ]
                    ],
                    'form-role' => 'save'
                ],
                'sort_order' => 90
            ];
        }
        return $data;
    }
}
