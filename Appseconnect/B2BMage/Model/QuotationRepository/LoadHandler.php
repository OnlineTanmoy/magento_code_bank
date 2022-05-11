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
namespace Appseconnect\B2BMage\Model\QuotationRepository;

use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor;
use Magento\Quote\Api\Data\CartExtensionFactory;

/**
 * Class LoadHandler
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class LoadHandler
{

    /**
     * ShippingAssignmentProcessor
     *
     * @var ShippingAssignmentProcessor
     */
    public $shippingAssignmentProcessor;

    /**
     * CartExtensionFactory
     *
     * @var CartExtensionFactory
     */
    public $cartExtensionFactory;

    /**
     * LoadHandler constructor.
     *
     * @param ShippingAssignmentProcessor $shippingAssignmentProcessor ShippingAssignmentProcessor
     * @param CartExtensionFactory        $cartExtensionFactory        CartExtensionFactory
     */
    public function __construct(
        ShippingAssignmentProcessor $shippingAssignmentProcessor,
        CartExtensionFactory $cartExtensionFactory
    ) {
    
        $this->shippingAssignmentProcessor = $shippingAssignmentProcessor;
        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    /**
     * Load
     *
     * @param QuoteInterface $quote Quote
     *
     * @return QuoteInterface
     */
    public function load(QuoteInterface $quote)
    {
        $quote->setItems($quote->getAllVisibleItems());
        return $quote;
    }
}
