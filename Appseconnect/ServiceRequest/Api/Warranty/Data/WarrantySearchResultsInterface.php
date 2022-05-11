<?php

namespace Appseconnect\ServiceRequest\Api\Warranty\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ServiceSearchResultsInterface
 * @package Appseconnect\ServiceRequest\Api\Warranty\Data
 */
interface WarrantySearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
