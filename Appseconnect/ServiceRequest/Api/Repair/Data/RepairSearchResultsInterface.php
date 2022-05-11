<?php

namespace Appseconnect\ServiceRequest\Api\Repair\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RepairSearchResultsInterface
 * @package Appseconnect\ServiceRequest\Api\Repair\Data
 */
interface RepairSearchResultsInterface extends SearchResultsInterface
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