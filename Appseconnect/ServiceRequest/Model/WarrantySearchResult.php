<?php

namespace Appseconnect\ServiceRequest\Model;

use Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantySearchResultsInterface;
use Magento\Framework\Api\SearchResults;
use Appseconnect\ServiceRequest\Model\WarrantyData;

/**
 * Class WarrantyRequestSearchResult
 * @package Appseconnect\ServiceRequest\Model
 */
class WarrantySearchResult extends SearchResults implements WarrantySearchResultsInterface
{

    /**
     * @return \Magento\Framework\Api\AbstractExtensibleObject[]|\Magento\Framework\Api\ExtensibleDataInterface[]|mixed|null
     */
    public function getItems(){
        return $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param array $items
     * @return WarrantyRequestSearchResult|SearchResults|\Magento\Framework\Api\SearchResultsInterface
     */
    public function setItems(array $items){
        return $this->setData(self::KEY_ITEMS, $items);
    }

}
