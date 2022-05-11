<?php

/**
 * Namespace
 *
 * @category Block/CategoryDiscount
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\CategoryDiscount\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Order
 *
 * @category Block/CategoryDiscount
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

class Category extends \Magento\Backend\Block\Template implements TabInterface
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * CustomerFactory class variable
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Initialize class variable
     *
     * @param \Magento\Backend\Block\Template\Context $context         Context variable
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory variable
     * @param \Magento\Framework\Registry             $registry        Registry variable
     * @param array                                   $data            Blank array
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
     * Return Customer id from registry
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category Discount');
    }

    /**
     * Return Tab Title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Discount');
    }

    /**
     * Show tab Based on the Customer type
     *
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            $customerDetail = $this->customerFactory->create()->load($this->getCustomerId());
            $customerType = $customerDetail->getData('customer_type');
            return ($customerType == 4) ? true : false;
        }
        return false;
    }

    /**
     * Hide is customer Id not found
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
            'b2bmage/categorydiscount/index_discount', [
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
