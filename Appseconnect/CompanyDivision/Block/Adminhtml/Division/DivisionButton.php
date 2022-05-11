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
namespace Appseconnect\CompanyDivision\Block\Adminhtml\Division;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

class DivisionButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Authorization
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    public $authorization;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * ContactpersonButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context   $context         context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory customer
     * @param \Magento\Framework\Registry             $registry        registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->authorization = $context->getAuthorization();
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $registry);
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $customerDetail = $this->customerFactory->create()->load($customerId);
        $customerType = $customerDetail->getData('customer_type');
        if ($customerType == 4) {
            $data = [];
            if ($customerId && $this->authorization->isAllowed('Magento_Sales::create')) {
                $data = [
                    'label' => __('Add Division'),
                    'on_click' => sprintf("location.href = '%s';", $this->getCreateDivisionUrl()),
                    'class' => 'add',
                    'sort_order' => 40
                ];
            }
            return $data;
        }
    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getCreateDivisionUrl()
    {
        return $this->getUrl(
            'division/division/new', [
                'customer_id' => $this->getCustomerId(),
                'is_division' => 1
            ]
        );
    }
}
