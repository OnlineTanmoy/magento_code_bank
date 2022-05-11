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
namespace Appseconnect\B2BMage\Api\Quotation\Data;

/**
 * Interface QuoteSearchResultsInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuoteSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get quotes list.
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface[]
     */
    public function getItems();

    /**
     * Set quotes list.
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface[] $items items
     *
     * @return $this
     */
    public function setItems(array $items);
}
