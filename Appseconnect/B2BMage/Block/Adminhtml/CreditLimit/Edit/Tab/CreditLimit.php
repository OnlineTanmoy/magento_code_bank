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
namespace Appseconnect\B2BMage\Block\Adminhtml\CreditLimit\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\CustomerFactory;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Abstract Class CreditLimit
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreditLimit extends \Magento\Backend\Block\Template implements TabInterface
{

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

    /**
     * CreditLimit constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context         context
     * @param CustomerFactory                         $customerFactory customer
     * @param \Magento\Framework\Registry             $registry        registry
     * @param array                                   $data            data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
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
        return __('Credit Transaction');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Credit Transaction');
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
            'b2bmage/credit/listing', [
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
