<?php

namespace Appseconnect\ServiceRequest\Model\Data\Repair;

use Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class RepairInterfaceData
 * @package Appseconnect\ServiceRequest\Model\Data\Repair
 */
class RepairInterfaceData extends AbstractExtensibleObject implements RepairInterface
{
    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set  Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku(){
        return $this->_get(self::SKU);
    }

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku){
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Get Repair Cost
     *
     * @return float|null
     */
    public function getRepairCost(){
        return $this->_get(self::REPAIR_COST);
    }

    /**
     * Set Repair Cost
     *
     * @param float $repairCost
     * @return $this
     */
    public function setRepairCost($repairCost){
        return $this->setData(self::REPAIR_COST, $repairCost);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt(){
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt(){
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Product Descripton
     *
     * @return string|null
     */
    public function getProductDescription(){
        return $this->_get(self::PRODUCT_DESCRIPTION);
    }

    /**
     * Set Product Descripton
     *
     * @param string $productDescription
     * @return $this
     */
    public function setProductDescription($productDescription){
        return $this->setData(self::PRODUCT_DESCRIPTION, $productDescription);
    }

}
