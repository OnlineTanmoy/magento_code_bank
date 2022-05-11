<?php

namespace Appseconnect\ServiceRequest\Model;

use Magento\Framework\Api\SearchResults;
use Appseconnect\ServiceRequest\Api\Service\Data\ServiceSearchResultsInterface;
use Appseconnect\ServiceRequest\Model\Data\ServiceInterfaceData;

/**
 * Class ServiceRequestSearchResult
 * @package Appseconnect\ServiceRequest\Model
 */
class ServiceRequestSearchResult extends SearchResults implements ServiceSearchResultsInterface
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
