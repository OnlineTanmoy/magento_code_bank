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

use Magento\Framework\Mail\Template\TransportBuilder;
use Appseconnect\B2BMage\Model\Quote\Email\Container\IdentityInterface;
use Appseconnect\B2BMage\Model\Quote\Email\Container\Template;

/**
 * Class SenderBuilder
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SenderBuilder
{

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
     * TransportBuilder
     *
     * @var TransportBuilder
     */
    public $transportBuilder;

    /**
     * SenderBuilder constructor.
     *
     * @param Template          $templateContainer TemplateContainer
     * @param IdentityInterface $identityContainer IdentityContainer
     * @param TransportBuilder  $transportBuilder  TransportBuilder
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder
    ) {
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Prepare and send email message
     *
     * @return void
     */
    public function send()
    {
        $this->configureEmailTemplate();
        
        $this->transportBuilder->addTo(
            $this->identityContainer->getCustomerEmail(),
            $this->identityContainer->getCustomerName()
        );
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }

    /**
     * Configure email template
     *
     * @return void
     */
    public function configureEmailTemplate()
    {
        $this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
        $this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
        $this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
        $this->transportBuilder->setFrom($this->identityContainer->getEmailIdentity());
    }
}
