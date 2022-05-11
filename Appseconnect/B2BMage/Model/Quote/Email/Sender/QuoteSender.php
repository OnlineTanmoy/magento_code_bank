<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Model\Quote\Email\Sender;

use Appseconnect\B2BMage\Model\Quote;
use Appseconnect\B2BMage\Model\Quote\Email\Container\QuoteIdentity;
use Appseconnect\B2BMage\Model\Quote\Email\Container\Template;
use Appseconnect\B2BMage\Model\Quote\Email\Sender;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

/**
 * Class CategoryDiscountData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteSender extends Sender
{

    /**
     * PaymentHelper
     *
     * @var PaymentHelper
     */
    public $paymentHelper;

    /**
     * OrderResource
     *
     * @var OrderResource
     */
    public $orderResource;

    /**
     * Global configuration storage.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $globalConfig;

    /**
     * Renderer
     *
     * @var Renderer
     */
    public $addressRenderer;

    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    public $eventManager;

    /**
     * Factory
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    public $objectFactory;

    /**
     * QuoteSender constructor.
     *
     * @param Template                                           $templateContainer    TemplateContainer
     * @param \Magento\Framework\DataObject\Factory              $objectFactory        ObjectFactory
     * @param QuoteIdentity                                      $identityContainer    IdentityContainer
     * @param Quote\Email\SenderBuilderFactory                   $senderBuilderFactory SenderBuilderFactory
     * @param \Psr\Log\LoggerInterface                           $logger               Logger
     * @param Renderer                                           $addressRenderer      AddressRenderer
     * @param PaymentHelper                                      $paymentHelper        PaymentHelper
     * @param OrderResource                                      $orderResource        OrderResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig         GlobalConfig
     * @param ManagerInterface                                   $eventManager         EventManager
     */
    public function __construct(
        Template $templateContainer,
        \Magento\Framework\DataObject\Factory $objectFactory,
        QuoteIdentity $identityContainer,
        \Appseconnect\B2BMage\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        ManagerInterface $eventManager
    ) {

        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer
        );
        $this->paymentHelper = $paymentHelper;
        $this->objectFactory = $objectFactory;
        $this->orderResource = $orderResource;
        $this->globalConfig = $globalConfig;
        $this->addressRenderer = $addressRenderer;
        $this->eventManager = $eventManager;
    }

    /**
     * Sends order email to the customer.
     *
     * Email will be sent immediately in two cases:
     *
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     *
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     *
     * @param Quote  $quote  Quote
     * @param string $action Action
     *
     * @return bool
     */
    public function send(Quote $quote, $action = null)
    {
        $quote->setSendEmail(true);
        if ($this->checkAndSend($quote, $action)) {
            $quote->setEmailSent(true);
            return true;
        }

        return false;
    }

    /**
     * Prepare email template with variables
     *
     * @param Quote  $quote  Quote
     * @param string $action Action
     *
     * @return void
     */
    public function prepareTemplate(Quote $quote, $action = null)
    {
        $transport = [
            'quote' => $quote,
            'store' => $quote->getStore()
        ];
        $transport = $this->objectFactory->create($transport);

        $this->templateContainer->setTemplateVars($transport->getData());

        parent::prepareTemplate($quote, $action);
    }

    /**
     * GetPaymentHtml
     *
     * @param Order $order Order
     *
     * @return string
     */
    public function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()
                ->getStoreId()
        );
    }
}
