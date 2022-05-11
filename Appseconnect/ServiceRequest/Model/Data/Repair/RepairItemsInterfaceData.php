<?php

namespace Appseconnect\ServiceRequest\Model\Data\Repair;

use Appseconnect\ServiceRequest\Api\Repair\Data\RepairItemsInterface;
use Appseconnect\ServiceRequest\Model\Data\RepairInterfaceData;

/**
 * Class RepairItemsInterfaceData
 * @package Appseconnect\ServiceRequest\Model\Data\Repair
 */
class RepairItemsInterfaceData implements RepairItemsInterface
{

    /**
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[]
     */
    public function getItems(){
        return $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[] $items
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface
     *
     */
    public function setItems(\Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface $items){
        return $this->setData(self::KEY_ITEMS, $items);
    }

}
