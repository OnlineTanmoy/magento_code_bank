<?php

namespace Appseconnect\ServiceRequest\Api\Service\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ServiceSearchResultsInterface
 * @package Appseconnect\ServiceRequest\Api\Service\Data
 */
interface ServiceSearchResultsInterface extends SearchResultsInterface
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