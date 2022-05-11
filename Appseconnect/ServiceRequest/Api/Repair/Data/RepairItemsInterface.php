<?php

namespace Appseconnect\ServiceRequest\Api\Repair\Data;

/**
 * Interface RepairItemsInterface
 * @package Appseconnect\ServiceRequest\Api\Repair\Data
 */
interface RepairItemsInterface
{
    const KEY_ITEMS = 'items';

    /**
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface
     */
    public function getItems();

    /**
     * @param \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[] $items
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface
     */
    public function setItems(\Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface $items);
}