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
use Magento\Catalog\Model\Session;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Abstract Class BackButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Customer account management
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * Catalog session
     *
     * @var Session
     */
    public $catalogSession;

    /**
     * BackButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context                   context
     * @param Session                               $catalogSession            catalog session
     * @param \Magento\Framework\Registry           $registry                  registry
     * @param AccountManagementInterface            $customerAccountManagement Customer acoount managment
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Session $catalogSession,
        \Magento\Framework\Registry $registry,
        AccountManagementInterface $customerAccountManagement
    ) {
        parent::__construct($context, $registry);
        $this->catalogSession = $catalogSession;
        $this->customerAccountManagement = $customerAccountManagement;
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => 'setLocation("' . $this->getDeleteUrl() . '")'
        ];
        return $data;
    }

    /**
     * Get Delete Url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        $parentCustomerId = $this->catalogSession->getParentCustomerId();
        return $this->getUrl(
            'customer/index/edit/', [
            'id' => $parentCustomerId
            ]
        );
    }
}
