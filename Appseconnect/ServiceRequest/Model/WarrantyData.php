<?php

namespace Appseconnect\ServiceRequest\Model;

class WarrantyData extends \Magento\Framework\Api\AbstractExtensibleObject implements \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get conytactperson id
     *
     * @return int|null
     */
    public function getContactpersonId()
    {
        return $this->_get(self::CONTACTPERSON_ID);
    }

    /**
     * Set conytactperson id
     *
     * @param int $contactpersonId
     * @return $this
     */
    public function setContactpersonId($contactpersonId)
    {
        return $this->setData(self::CONTACTPERSON_ID, $contactpersonId);
    }

    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set Customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set mfr serial no
     *
     * @param string $mfrSerialNo
     * @return $this
     */
    public function setMfrSerialNo($mfrSerialNo)
    {
        return $this->setData(self::MFR_SERIAL_NO, $mfrSerialNo);
    }

    /**
     * Get mfr serial no
     *
     * @return string|null
     */
    public function getMfrSerialNo()
    {
        return $this->_get(self::MFR_SERIAL_NO);
    }


    /**
     * Get copack serial number
     *
     * @return string|null
     */
    public function getCopackSerialNo()
    {
        return $this->_get(self::COPACK_SERIAL_NO);
    }

    /**
     * Set copack serial number
     *
     * @param string $copackSerialNo
     * @return $this
     */
    public function setCopackSerialNo($copackSerialNo)
    {
        return $this->setData(self::COPACK_SERIAL_NO, $copackSerialNo);
    }

    /**
     * Get sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getWarrantyStartDate()
    {
        return $this->_get(self::START_DATE);
    }

    /**
     * Set start date
     *
     * @param string $warrantyStartDate
     * @return $this
     */
    public function setWarrantyStartDate($warrantyStartDate)
    {
        return $this->setData(self::START_DATE, $warrantyStartDate);
    }

    /**
     * Get end date
     *
     * @return string|null
     */
    public function getWarrantyEndDate()
    {
        return $this->_get(self::END_DATE);
    }

    /**
     * Set end date
     *
     * @param string $warrantyEndDate
     * @return $this
     */
    public function setWarrantyEndDate($warrantyEndDate)
    {
        return $this->setData(self::END_DATE, $warrantyEndDate);
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get contract status
     *
     * @return mixed|string|null
     */
    public function getContractStatus()
    {
        return $this->_get(self::CONTRACT_STATUS);
    }

    /**
     * Set contract status
     *
     * @param string $contractStatus
     * @return $this
     */
    public function setContractStatus($contractStatus)
    {
        return $this->setData(self::CONTRACT_STATUS, $contractStatus);
    }

    /**
     * Get equipment card number
     *
     * @return mixed|string|null
     */
    public function getEquipmentCardNo()
    {
        return $this->_get(self::EQUIPMENT_CARD_NO);
    }

    /**
     * Set equipment card number
     *
     * @param string $equipmentCardNo
     * @return $this
     */
    public function setEquipmentCardNo($equipmentCardNo)
    {
        return $this->setData(self::EQUIPMENT_CARD_NO, $equipmentCardNo);
    }

    /**
     * Get equipment creation date
     *
     * @return mixed|string|null
     */
    public function getEquipmentCreationDate()
    {
        return $this->_get(self::EQUIPMENT_CREATION_DATE);
    }

    /**
     * Set equipment creation date
     *
     * @param string $equipmentCreationDate
     * @return $this
     */
    public function setEquipmentCreationDate($equipmentCreationDate)
    {
        return $this->setData(self::EQUIPMENT_CREATION_DATE, $equipmentCreationDate);
    }

    /**
     * Get contract number
     *
     * @return mixed|string|null
     */
    public function getContractNo()
    {
        return $this->_get(self::CONTRACT_NO);
    }

    /**
     * Set contract number
     *
     * @param string $contractNo
     * @return $this
     */
    public function setContractNo($contractNo)
    {
        return $this->setData(self::CONTRACT_NO, $contractNo);
    }

    /**
     * Get contract creation date
     *
     * @return mixed|string|null
     */
    public function getContractCreationDate()
    {
        return $this->_get(self::CONTRACT_CREATION_DATE);
    }

    /**
     * Set contract creation date
     *
     * @param string $contractCreationDate
     * @return $this
     */
    public function setContractCreationDate($contractCreationDate)
    {
        return $this->setData(self::CONTRACT_CREATION_DATE, $contractCreationDate);
    }

    /**
     * Get termination date
     *
     * @return mixed|string|null
     */
    public function getTerminationDate()
    {
        return $this->_get(self::TERMINATION_DATE);
    }

    /**
     * Set termination date
     *
     * @param string $terminationDate
     * @return $this
     */
    public function setTerminationDate($terminationDate)
    {
        return $this->setData(self::TERMINATION_DATE, $terminationDate);
    }

    /**
     * Get product name
     *
     * @return mixed|string|null
     */
    public function getProductName()
    {
        return $this->_get(self::PRODUCT_NAME);
    }

    /**
     * Set product name
     *
     * @param $productName
     * @return void
     */
    public function setProductName($productName)
    {
        $this->setData(self::PRODUCT_NAME, $productName);
    }
}
