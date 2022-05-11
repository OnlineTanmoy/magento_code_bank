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
use Magento\Customer\Model\Session;

/**
 * Class CatalogBlockProductListCollection
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CheckoutCartSaveAfter implements ObserverInterface
{

    /**
     * Session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    protected $quotationHelper;

    /**
     * CheckoutCartSaveAfter constructor.
     *
     * @param Session                                     $session         Session
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper QuotationHelper
     */
    public function __construct(
        Session $session,
        \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper
    ) {
        $this->customerSession = $session;
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart = $observer->getEvent()->getData('cart');
        $quote = $cart->getData('quote');
        $quoteDetail = $this->customerSession->getQuotationData();
        $count = $cart->getQuote()->getItemsCount();
        if (!$quote->getQuotationInfo() && $quoteDetail) {
            $quoteDetail = $this->quotationHelper->getQuotationInfo($quoteDetail, "json");
            $quote->setQuotationInfo($quoteDetail);
            $quote->save();
        }
        if ($count < 1) {
            $quote->setQuotationInfo(null);
            $quote->save();
        }
        $this->customerSession->unsQuotationData();
        return $this;
    }
}
