<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Observer\Quotation;

use Magento\Framework\Event\ObserverInterface;
use Appseconnect\B2BMage\Model\Quote\Email\Sender\QuoteSender;

/**
 * Class ActionMailObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ActionMailObserver implements ObserverInterface
{
    /**
     * QuoteSender
     *
     * @var QuoteSender
     */
    public $quoteSender;
    
    /**
     * LoggerInterface
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * ActionMailObserver constructor.
     *
     * @param QuoteSender              $quoteSender QuoteSender
     * @param \Psr\Log\LoggerInterface $logger      Logger
     */
    public function __construct(QuoteSender $quoteSender, \Psr\Log\LoggerInterface $logger)
    {
        $this->quoteSender = $quoteSender;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $action = $observer->getAction();
        
        try {
            $this->quoteSender->send($quote, $action);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
