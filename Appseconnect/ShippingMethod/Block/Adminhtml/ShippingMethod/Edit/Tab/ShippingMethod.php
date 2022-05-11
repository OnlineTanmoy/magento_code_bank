<?php

namespace Appseconnect\ShippingMethod\Block\Adminhtml\ShippingMethod\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\CustomerFactory;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Abstract Class ShippingMethod
 *
 */
class ShippingMethod extends \Magento\Backend\Block\Template implements TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'shippingmethod/listing.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Customer
     *
     * @var CustomerFactory
     */
    public $customerFactory;

    public $allShippingMethods;

    public $shippingMethodCollectionFactory;

    /**
     * ShippingMethod constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context context
     * @param CustomerFactory $customerFactory customer
     * @param \Magento\Framework\Registry $registry registry
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $allShippingMethods
     * @param \Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod\CollectionFactory $shippingMethodCollectionFactory
     * @param array $data data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\Config\Source\Allmethods $allShippingMethods,
        \Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod\CollectionFactory $shippingMethodCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        $this->allShippingMethods = $allShippingMethods;
        $this->shippingMethodCollectionFactory = $shippingMethodCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Delivery Methods');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Delivery Methods');
    }

    /**
     * Can show tab
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
     * Is hidden
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
            'appseconnect/shippingmethod/listing', [
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
        return false;
    }

    /**
     * All Shipping Methods
     *
     * @return Array
     */
    public function getAllShippingMethods()
    {
        return $this->allShippingMethods->toOptionArray();
    }

    public function getFormAction()
    {
        return $this->getUrl('appseconnect/shippingmethod/save', ['_secure' => true]);
    }

    /**
     * @param int $customerId
     * @return array
     */
    public function getShippingMethodData($customerId)
    {
        $shippingMethodData = $this->shippingMethodCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);
        return $shippingMethodData->getData();
    }
}