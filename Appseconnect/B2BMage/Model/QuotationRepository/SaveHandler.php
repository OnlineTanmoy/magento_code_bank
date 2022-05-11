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
use Appseconnect\B2BMage\Model\Quote\Product\QuoteProductPersister;
use Magento\Framework\Exception\InputException;
use Magento\Quote\Model\Quote\Address\BillingAddressPersister;
use Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentPersister;

/**
 * Class SaveHandler
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SaveHandler
{

    /**
     * QuoteProductPersister
     *
     * @var \Appseconnect\B2BMage\Model\Quote\Product\QuoteProductPersister
     */
    public $quoteItemPersister;

    /**
     * BillingAddressPersister
     *
     * @var \Magento\Quote\Model\Quote\Address\BillingAddressPersister
     */
    public $billingAddressPersister;

    /**
     * Quote
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Quote
     */
    public $quoteResourceModel;

    /**
     * ShippingAssignmentPersister
     *
     * @var \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentPersister
     */
    public $shippingAssignmentPersister;

    /**
     * SaveHandler constructor.
     *
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Quote $quoteResource               QuoteResource
     * @param QuoteProductPersister                           $quoteItemPersister          QuoteItemPersister
     * @param BillingAddressPersister                         $billingAddressPersister     BillingAddressPersister
     * @param ShippingAssignmentPersister                     $shippingAssignmentPersister ShippingAssignmentPersister
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ResourceModel\Quote $quoteResource,
        QuoteProductPersister $quoteItemPersister,
        BillingAddressPersister $billingAddressPersister,
        ShippingAssignmentPersister $shippingAssignmentPersister
    ) {

        $this->quoteResourceModel = $quoteResource;
        $this->quoteItemPersister = $quoteItemPersister;
        $this->billingAddressPersister = $billingAddressPersister;
        $this->shippingAssignmentPersister = $shippingAssignmentPersister;
    }

    /**
     * Save
     *
     * @param QuoteInterface $quote Quote
     * @param $flag  Flag
     *
     * @return QuoteInterface
     */
    public function save(QuoteInterface $quote, $flag)
    {
        $items = $quote->getItems();
        if ($items) {
            foreach ($items as $item) {
                if (!$item->isDeleted()) {
                    $quote->setLastAddedItem($this->saveQuoteItem($quote, $item, $flag));
                }
            }
        }

        $this->quoteResourceModel->save($quote->collectTotals());
        return $quote;
    }

    /**
     * SaveQuoteItem
     *
     * @param \Appseconnect\B2BMage\Model\Quote        $quote Quote
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $item  Item
     * @param boolean                                  $flag  Flag
     *
     * @return mixed
     */
    public function saveQuoteItem($quote, $item, $flag)
    {
        return $this->quoteItemPersister->save($quote, $item, $flag);
    }

    /**
     * ProcessShippingAssignment
     *
     * @param \Magento\Quote\Model\Quote $quote Quote
     *
     * @return void
     * @throws InputException
     */
    public function processShippingAssignment($quote)
    {
        $extensionAttributes = $quote->getExtensionAttributes();
        if (!$quote->isVirtual()
            && $extensionAttributes
            && $extensionAttributes->getShippingAssignments()
        ) {
            $shippingAssignments = $extensionAttributes->getShippingAssignments();
            if (count($shippingAssignments) > 1) {
                throw new InputException(__("Only 1 shipping assignment can be set"));
            }
            $this->shippingAssignmentPersister->save($quote, $shippingAssignments[0]);
        }
    }
}
