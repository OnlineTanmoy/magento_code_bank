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

namespace Appseconnect\B2BMage\Model\Quote\Email;

use Appseconnect\B2BMage\Model\Quote;
use Appseconnect\B2BMage\Model\Quote\Email\Container\IdentityInterface;
use Appseconnect\B2BMage\Model\Quote\Email\Container\Template;
use Magento\Sales\Model\Order\Address\Renderer;

/**
 * Abstract Class Sender
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class Sender
{

    /**
     * SenderBuilderFactory
     *
     * @var \Appseconnect\B2BMage\Model\Quote\Email\SenderBuilderFactory
     */
    public $senderBuilderFactory;

    /**
     * Template
     *
     * @var Template
     */
    public $templateContainer;

    /**
     * IdentityInterface
     *
     * @var IdentityInterface
     */
    public $identityContainer;

    /**
     * LoggerInterface
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * Renderer
     *
     * @var Renderer
     */
    public $addressRenderer;

    /**
     * Sender constructor.
     *
     * @param Template                 $templateContainer    TemplateContainer
     * @param IdentityInterface        $identityContainer    IdentityContainer
     * @param SenderBuilderFactory     $senderBuilderFactory SenderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger               Logger
     * @param Renderer                 $addressRenderer      AddressRenderer
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        \Appseconnect\B2BMage\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer
    ) {

        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->senderBuilderFactory = $senderBuilderFactory;
        $this->logger = $logger;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * CheckAndSend
     *
     * @param Quote $quote  Quote
     * @param null  $action Action
     *
     * @return bool
     */
    public function checkAndSend(Quote $quote, $action = null)
    {
        $this->identityContainer->setStore($quote->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($quote, $action);

        $sender = $this->getSender();

        try {
            $sender->send();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return true;
    }

    /**
     * PrepareTemplate
     *
     * @param Quote $quote  Quote
     * @param null  $action Action
     *
     * @return void
     */
    public function prepareTemplate(Quote $quote, $action = null)
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($quote->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $quote->getBillingAddress()->getName();
        } else {
            switch ($action) {
            case "submit":
                $templateId = $this->identityContainer->getNewTemplateId();
                break;
            case "approve":
                $templateId = $this->identityContainer->getApproveTemplateId();
                break;
            case "hold":
                $templateId = $this->identityContainer->getHoldTemplateId();
                break;
            case "unhold":
                $templateId = $this->identityContainer->getUnholdTemplateId();
                break;
            case "cancel":
                $templateId = $this->identityContainer->getCancelTemplateId();
                break;
            case "comment":
                $templateId = $this->identityContainer->getTemplateId();
                break;
            default:
                break;
            }
            $customerName = $quote->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($quote->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

    /**
     * GetSender
     *
     * @return Sender
     */
    public function getSender()
    {
        return $this->senderBuilderFactory->create(
            [
                'templateContainer' => $this->templateContainer,
                'identityContainer' => $this->identityContainer
            ]
        );
    }

    /**
     * GetTemplateOptions
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->identityContainer->getStore()->getStoreId()
        ];
    }

    /**
     * GetFormattedShippingAddress
     *
     * @param Order $order Order
     *
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual() ?
            null :
            $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * GetFormattedBillingAddress
     *
     * @param Order $order Order
     *
     * @return string|null
     */
    public function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
}
