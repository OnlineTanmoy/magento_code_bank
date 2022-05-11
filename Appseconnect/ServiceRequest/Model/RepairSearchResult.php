<?php

namespace Appseconnect\ServiceRequest\Model;

use Magento\Framework\Api\SearchResults;
use Appseconnect\ServiceRequest\Api\Repair\Data\RepairSearchResultsInterface;
use Appseconnect\ServiceRequest\Model\Data\RepairInterfaceData;

/**
 * Class RepairSearchResult
 * @package Appseconnect\ServiceRequest\Model
 */
class RepairSearchResult extends SearchResults implements RepairSearchResultsInterface
{

    /**
     * @return \Magento\Framework\Api\AbstractExtensibleObject[]|\Magento\Framework\Api\ExtensibleDataInterface[]|mixed|null
     */
    public function getItems(){
        return $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param array $items
     * @return ServiceRequestSearchResult|SearchResults|\Magento\Framework\Api\SearchResultsInterface
     */
    public function setItems(array $items){
        return $this->setData(self::KEY_ITEMS, $items);
    }

}
