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
namespace Appseconnect\B2BMage\Block\Adminhtml\Sales\Approver;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Tabs
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Tab extends \Magento\Backend\Block\Template implements TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Tab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context         Context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param \Magento\Framework\Registry             $registry        Registry
     * @param array                                   $data            Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }
    
    /**
     * GetCustomerId
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    
    /**
     * GetTabLabel
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Approvers');
    }
    
    /**
     * GetTabTitle
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Approvers');
    }
    
    /**
     * CanShowTab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            $customerDetail = $this->customerFactory->create()->load($this->getCustomerId());
            $customerType=$customerDetail->getData('customer_type');
            return ($customerType==4)?true:false;
        }
        return false;
    }
    
    /**
     * IsHidden
     *
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
    
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl(
            'b2bmage/approver/order',
            [
                '_current' => true
            ]
        );
    }
    
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
