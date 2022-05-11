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
namespace Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Contact;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

/**
 * Abstract Class ContactpersonButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ContactpersonButton extends GenericButton implements ButtonProviderInterface
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
                    'label' => __('Add Contact Person'),
                    'on_click' => sprintf("location.href = '%s';", $this->getCreateOrderUrl()),
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
    public function getCreateOrderUrl()
    {
        return $this->getUrl(
            'b2bmage/contact/new', [
            'customer_id' => $this->getCustomerId(),
            'is_contactperson' => 1
            ]
        );
    }
}
