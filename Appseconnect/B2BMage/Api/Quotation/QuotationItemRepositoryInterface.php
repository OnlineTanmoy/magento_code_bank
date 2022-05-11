<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\Quotation;

/**
 * Interface QuotationItemRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuotationItemRepositoryInterface
{
    /**
     * Lists items that are assigned to a specified quote.
     *
     * @param int $quoteId The Quote ID.
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     */
    public function getList($quoteId);
    
    /**
     * Add/update the specified quotation item.
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface $quoteItem The item.
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface Item.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quotation does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the quote.
     * @throws \Magento\Framework\Exception\InputException The specified item or quotation is not valid.
     */
    public function save(\Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface $quoteItem);
    /**
     * Removes the specified item from the specified quote.
     *
     * @param int $quoteId The quote ID.
     * @param int $itemId  The item ID of the item to be removed.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or quote does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteById($quoteId, $itemId);
}
