<?php

namespace Appseconnect\ServiceRequest\Api\Warranty\Data;

/**
 * Customer Tierprice.
 * @api
 */
interface WarrantyInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
	 * Constants defined for keys of the data array. Identical to the name of the getter in snake case
	 */
    const ID = 'id';
    const CONTACTPERSON_ID = 'contactperson_id';
    const CUSTOMER_ID = 'customer_id';
    const MFR_SERIAL_NO = 'mfr_serial_no';
    const COPACK_SERIAL_NO = 'copack_serial_no';
    const SKU = 'sku';
    const START_DATE = 'warranty_start_date';
    const END_DATE = 'warranty_end_date';

    const STATUS = 'status';
    const CONTRACT_STATUS = 'contract_status';
    const EQUIPMENT_CARD_NO = 'equipment_card_no';
    const EQUIPMENT_CREATION_DATE = 'equipment_creation_date';
    const CONTRACT_NO = 'contract_no';
    const CONTRACT_CREATION_DATE = 'contract_creation_date';
    const TERMINATION_DATE = 'termination_date';
    const PRODUCT_NAME = 'product_name';
    /**#@-*/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Get conytactperson id
     *
     * @return int|null
     */
    public function getContactpersonId();

    /**
     * Set conytactperson id
     *
     * @param int $contactpersonId
     * @return $this
     */
    public function setContactpersonId($contactpersonId);
    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set mfr serial no
     *
     * @param string $mfrSerialNo
     * @return $this
     */
    public function setMfrSerialNo($mfrSerialNo);

    /**
     * Get mfr serial no
     *
     * @return string|null
     */
    public function getMfrSerialNo();


    /**
     * Get copack serial number
     *
     * @return string|null
     */
    public function getCopackSerialNo();

    /**
     * Set copack serial number
     *
     * @param string $copackSerialNo
     * @return $this
     */
    public function setCopackSerialNo($copackSerialNo);
    
    /**
     * Get sku
     *
     * @return string|null
     */
    public function getSku();
    
    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getWarrantyStartDate();

    /**
     * Set start date
     *
     * @param string $warrantyStartDate
     * @return $this
     */
    public function setWarrantyStartDate($warrantyStartDate);

    /**
     * Get end date
     *
     * @return string|null
     */
    public function getWarrantyEndDate();

    /**
     * Set end date
     *
     * @param string $warrantyEndDate
     * @return $this
     */
    public function setWarrantyEndDate($warrantyEndDate);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get contract status
     *
     * @return string|null
     */
    public function getContractStatus();

    /**
     * Set contract status
     *
     * @param string $contractStatus
     * @return $this
     */
    public function setContractStatus($contractStatus);

    /**
     * Get equipment card number
     *
     * @return string|null
     */
    public function getEquipmentCardNo();

    /**
     * Set equipment card number
     *
     * @param string $equipmentCardNo
     * @return $this
     */
    public function setEquipmentCardNo($equipmentCardNo);

    /**
     * Get equipment creation date
     *
     * @return string|null
     */
    public function getEquipmentCreationDate();

    /**
     * Set equipment creation date
     *
     * @param string $equipmentCreationDate
     * @return $this
     */
    public function setEquipmentCreationDate($equipmentCreationDate);

    /**
 * Get contract number
 *
 * @return string|null
 */
    public function getContractNo();

    /**
     * Set contract number
     *
     * @param string $contractNo
     * @return $this
     */
    public function setContractNo($contractNo);

    /**
     * Get contract creation date
     *
     * @return string|null
     */
    public function getContractCreationDate();

    /**
     * Set contract creation date
     *
     * @param string $contractCreationDate
     * @return $this
     */
    public function setContractCreationDate($contractCreationDate);

    /**
     * Get termination date
     *
     * @return string|null
     */
    public function getTerminationDate();

    /**
     * Set termination date
     *
     * @param string $terminationDate
     * @return $this
     */
    public function setTerminationDate($terminationDate);

    /**
     * Get product name
     *
     * @return mixed|string|null
     */
    public function getProductName();

    /**
     * Set product name
     *
     * @param $productName
     * @return void
     */
    public function setProductName($productName);
}
