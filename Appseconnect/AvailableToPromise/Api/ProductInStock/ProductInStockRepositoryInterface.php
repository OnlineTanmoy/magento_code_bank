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

namespace Appseconnect\AvailableToPromise\Api\ProductInStock;

use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface;
use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;


/**
 * Interface ProductInStockRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductInStockRepositoryInterface
{
    /**
     *
     * @param ProductInStockInterface $productInStock productInStock
     *
     * @return \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface
     */
    public function save(ProductInStockInterface $productInStock);

    /**
     * @param int $availabletopromiseId availabletopromise Id
     * @return \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface
     */
    public function get($availabletopromiseId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     *
     * @param int $availabletopromiseId availabletopromise Id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($availabletopromiseId);

}
