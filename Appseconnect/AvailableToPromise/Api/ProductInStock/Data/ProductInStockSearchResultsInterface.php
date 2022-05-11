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

namespace Appseconnect\AvailableToPromise\Api\ProductInStock\Data;


/**
 * Interface ProductInStockSearchResultsInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductInStockSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get test Complete list.
     *
     * @return \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface[]
     */
    public function getItems();

    /**
     * Set test Complete list.
     *
     * @param \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface[] $items items
     *
     * @return $this
     */
    public function setItems(array $items);
}
