<?php

namespace Appseconnect\ServiceRequest\Api\Repair\Data;

/**
 * Interface RepairInterface
 * @package Appseconnect\ServiceRequest\Api\Repair\Data
 */
interface RepairInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
	 * Constants defined for keys of the data array. Identical to the name of the getter in snake case
	 */
    const ID = 'id';
    const SKU = 'sku';
    const REPAIR_COST = 'repair_cost';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRODUCT_DESCRIPTION = 'product_description';
    /**#@-*/

    /**
     * Get  id
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Set  id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get Repair Cost
     *
     * @return float|null
     */
    public function getRepairCost();

    /**
     * Set Repair Cost
     *
     * @param float $repairCost
     * @return $this
     */
    public function setRepairCost($repairCost);

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Product Descripton
     *
     * @return string|null
     */
    public function getProductDescription();

    /**
     * Set Product Descripton
     *
     * @param string $productDescription
     * @return $this
     */
    public function setProductDescription($productDescription);
}
