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
namespace Appseconnect\CompanyDivision\Block\Adminhtml\Division\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Abstract Class DeleteButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Customer accoint managemnt
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * DeleteButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context                   context
     * @param \Magento\Framework\Registry           $registry                  registry
     * @param AccountManagementInterface            $customerAccountManagement Customer account manager
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
        $canModify = $customerId && ! $this->customerAccountManagement->isReadonly($this->getCustomerId());
        $data = [];
        if ($customerId && $canModify) {
            $data = [
                'label' => __('Delete Division'),
                'class' => '',
                'on_click' => 'setLocation("' . $this->getDeleteUrl() . '")'
            ];
        }
        return $data;
    }

    /**
     * Get delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'division/division/delete', [
            'id' => $this->getCustomerId()
            ]
        );
    }
}
