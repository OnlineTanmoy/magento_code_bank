<?php

namespace Appseconnect\CompanyDivision\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Division extends \Magento\Backend\Block\Template implements TabInterface
{

    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    public function getTabLabel()
    {
        return __('Division List');
    }

    public function getTabTitle()
    {
        return __('Division List');
    }

    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            $customerDetail = $this->customerFactory->create()->load($this->getCustomerId());
            $customerType = $customerDetail->getData('customer_type');
            return ($customerType == 4) ? true : false;
        }
        return false;
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    public function getTabClass()
    {
        return '';
    }

    public function getTabUrl()
    {
        return $this->getUrl('division/division/listing', ['_current' => true]);
    }

    public function isAjaxLoaded()
    {
        return true;
    }
}
