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
use Appseconnect\B2BMage\Model\Quote\Email\Container\QuoteCommentIdentity;
use Appseconnect\B2BMage\Model\Quote\Email\Container\Template;
use Appseconnect\B2BMage\Model\Quote\Email\NotifySender;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class QuoteCommentSender
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteCommentSender extends NotifySender
{
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
     * QuoteCommentSender constructor.
     *
     * @param Template                         $templateContainer    TemplateContainer
     * @param QuoteCommentIdentity             $identityContainer    IdentityContainer
     * @param Quote\Email\SenderBuilderFactory $senderBuilderFactory SenderBuilderFactory
     * @param \Psr\Log\LoggerInterface         $logger               Logger
     * @param Renderer                         $addressRenderer      AddressRenderer
     * @param ManagerInterface                 $eventManager         EventManager
     */
    public function __construct(
        Template $templateContainer,
        QuoteCommentIdentity $identityContainer,
        \Appseconnect\B2BMage\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager
    ) {
    
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer
        );
        $this->addressRenderer = $addressRenderer;
        $this->eventManager = $eventManager;
    }

    /**
     * Send email to customer
     *
     * @param Quote  $quote           Quote
     * @param string $action          Action
     * @param string $commentProvider CommentProvider
     * @param bool   $notify          Notify
     * @param string $comment         Comment
     *
     * @return bool
     */
    public function send(
        Quote $quote,
        $action = null,
        $commentProvider = null,
        $notify = true,
        $comment = ''
    ) {
        $transport = [
            'quote' => $quote,
            'comment' => $comment,
            'comment_provider' => $commentProvider,
            'store' => $quote->getStore()
        ];
        
        $this->templateContainer->setTemplateVars($transport);
        
        return $this->checkAndSend($quote, $action, $notify);
    }
}
