<?php

declare(strict_types=1);

namespace Appseconnect\LoginAsCustomerAdmin\Plugin\Button;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\ToolbarInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\LoginAsCustomerAdminUi\Ui\Customer\Component\Button\DataProvider;
use Magento\LoginAsCustomerApi\Api\ConfigInterface;

/**
 * Plugin for \Magento\Backend\Block\Widget\Button\Toolbar.
 */
class ToolbarPlugin extends \Magento\LoginAsCustomerAdminUi\Plugin\Button\ToolbarPlugin
{
    public $customerFactory;
    /**
     * @var AuthorizationInterface
     */
    private $authorization;
    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * ToolbarPlugin constructor.
     * @param AuthorizationInterface $authorization
     * @param ConfigInterface $config
     * @param Escaper $escaper
     * @param DataProvider $dataProvider
     */
    public function __construct(
        AuthorizationInterface $authorization,
        ConfigInterface $config,
        Escaper $escaper,
        DataProvider $dataProvider,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    ) {
        $this->authorization = $authorization;
        $this->config = $config;
        $this->escaper = $escaper;
        $this->dataProvider = $dataProvider;
        $this->customerFactory = $customerFactory;

    }

    /**
     * Add Login as Customer button.
     *
     * @param ToolbarInterface $subject
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePushButtons(
        ToolbarInterface $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ): void {
        $nameInLayout = $context->getNameInLayout();
        $order = $this->getOrder($nameInLayout, $context);
        $customerType = 0;
        if (!empty($order['customer_id'])) {
            $customer = $this->customerFactory->create()->load($order['customer_id']);
            $customerType = $customer->getCustomerType();
        }
        if ($order
            && !empty($order['customer_id'])
            && $customerType == 1
            && $this->config->isEnabled()
            && $this->authorization->isAllowed('Magento_LoginAsCustomer::login')
        ) {
            $customerId = (int)$order['customer_id'];
            $buttonList->add(
                'guest_to_customer',
                $this->dataProvider->getData($customerId),
                -1
            );
        }
    }

    /**
     * Extract order data from context.
     *
     * @param string $nameInLayout
     * @param AbstractBlock $context
     * @return array|null
     */
    private function getOrder(string $nameInLayout, AbstractBlock $context)
    {
        switch ($nameInLayout) {
            case 'sales_order_edit':
                return $context->getOrder();
            case 'sales_invoice_view':
                return $context->getInvoice()->getOrder();
            case 'sales_shipment_view':
                return $context->getShipment()->getOrder();
            case 'sales_creditmemo_view':
                return $context->getCreditmemo()->getOrder();
        }

        return null;
    }
}
