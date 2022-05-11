<?php

namespace Appseconnect\ServiceRequest\Model\Data\Service;

use Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class ServiceInterfaceData
 * @package Appseconnect\ServiceRequest\Model\Data
 */
class ServiceInterfaceData extends AbstractExtensibleObject implements ServiceInterface
{
    /**
     * Get Entity Id
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set Entity Id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Model number
     *
     * @return string|null
     */
    public function getModelNumber(){
        return $this->_get(self::MODEL_NUMBER);

    }

    /**
     * Set Model Number
     *
     * @param string $modelNumber
     * @return $this
     */
    public function setModelNumber($modelNumber){
        return $this->setData(self::MODEL_NUMBER, $modelNumber);
    }

    /**
     * Get Product Description
     *
     * @return string|null
     */
    public function getProductDescription(){
        return $this->_get(self::PRODUCT_DESCRIPTION);
    }

    /**
     * Set Product Description
     *
     * @param string $productDescription
     * @return $this
     */
    public function setProductDescription($productDescription){
        return $this->setData(self::PRODUCT_DESCRIPTION, $productDescription);
    }

    /**
     * Get serial number
     *
     * @return string|null
     */
    public function getSerialNumber(){
        return $this->_get(self::SERIAL_NUMBER);
    }

    /**
     * Set serial number
     *
     * @param string $serialNumber
     * @return $this
     */
    public function setSerialNumber($serialNumber){
        return $this->setData(self::SERIAL_NUMBER, $serialNumber);
    }

    /**
     * Get copack serial number
     *
     * @return string|null
     */
    public function getCopackSerialNumber(){
        return $this->_get(self::COPACK_SERIAL_NUMBER);
    }

    /**
     * Set copack serial number
     *
     * @param string $copackSerialNumber
     * @return $this
     */
    public function setCopackSerialNumber($copackSerialNumber){
        return $this->setData(self::COPACK_SERIAL_NUMBER, $copackSerialNumber);
    }

    /**
     * Get short description
     *
     * @return string|null
     */
    public function getShortDescription(){
        return $this->_get(self::SHORT_DESCRIPTION);
    }

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription){
        return $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * Get detailed description
     *
     * @return string|null
     */
    public function getDetailedDescription(){
        return $this->_get(self::DETAILED_DESCRIPTION);
    }

    /**
     * Set detailed description
     *
     * @param string $detailedDescription
     * @return $this
     */
    public function setDetailedDescription($detailedDescription){
        return $this->setData(self::DETAILED_DESCRIPTION, $detailedDescription);
    }

    /**
     * Get safety 1
     *
     * @return string|null
     */
    public function getSafety1(){
        return $this->_get(self::SAFETY1);
    }

    /**
     * Set safety 1
     *
     * @param string $safety1
     * @return $this
     */
    public function setSafety1($safety1){
        return $this->setData(self::SAFETY1, $safety1);
    }

    /**
     * Get safety 2
     *
     * @return string|null
     */
    public function getSafety2(){
        return $this->_get(self::SAFETY2);
    }

    /**
     * Set safety 2
     *
     * @param string $safety2
     * @return $this
     */
    public function setSafety2($safety2){
        return $this->setData(self::SAFETY2, $safety2);
    }

    /**
     * Get safety 3
     *
     * @return string|null
     */
    public function getSafety3(){
        return $this->_get(self::SAFETY3);
    }

    /**
     * Set safety 3
     *
     * @param string $safety3
     * @return $this
     */
    public function setSafety3($safety3){
        return $this->setData(self::SAFETY3, $safety3);
    }

    /**
     * Get terms condition
     *
     * @return string|null
     */
    public function getTermsCondition(){
        return $this->_get(self::TERMS_CONDITIONS);
    }

    /**
     * Set terms condition
     *
     * @param string $termsCondition
     * @return $this
     */
    public function setTermsCondition($termsCondition){
        return $this->setData(self::TERMS_CONDITIONS, $termsCondition);
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus(){
        return $this->_get(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get post Date
     *
     * @return string|null
     */
    public function getPost(){
        return $this->_get(self::POST);
    }

    /**
     * Set Status
     *
     * @param string $post
     * @return $this
     */
    public function setPost($post){
        return $this->setData(self::POST, $post);
    }

    /**
     * Get Ra id
     *
     * @return string|null
     */
    public function getRaId(){
        return $this->_get(self::RA_ID);
    }

    /**
     * Set Ra id
     *
     * @param string $raId
     * @return $this
     */
    public function setRaId($raId){
        return $this->setData(self::RA_ID, $raId);
    }

    /**
     * Get Store id
     *
     * @return int|null
     */
    public function getStoreId(){
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set Store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId){
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get purchase order number
     *
     * @return string|null
     */
    public function getPurchaseOrderNumber(){
        return $this->_get(self::PURCHASE_ORDER_NUMBER);
    }

    /**
     * Set purchase order number
     *
     * @param string $purchaseOrderNumber
     * @return $this
     */
    public function setPurchaseOrderNumber($purchaseOrderNumber){
        return $this->setData(self::PURCHASE_ORDER_NUMBER, $purchaseOrderNumber);
    }

    /**
     * Get is warranty
     *
     * @return int|null
     */
    public function getIsWarranty(){
        return $this->_get(self::IS_WARRANTY);
    }

    /**
     * Set is warranty
     *
     * @param int $isWarranty
     * @return $this
     */
    public function setIsWarranty($isWarranty){
        return $this->setData(self::IS_WARRANTY, $isWarranty);
    }

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId(){
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId){
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get device type
     *
     * @return string|null
     */
    public function getDeviceType(){
        return $this->_get(self::DEVICE_TYPE);
    }

    /**
     * Set device type
     *
     * @param string $deviceType
     * @return $this
     */
    public function setDeviceType($deviceType){
        return $this->setData(self::DEVICE_TYPE, $deviceType);
    }

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName(){
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * Set customer name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName){
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get shipping address id
     *
     * @return string|null
     */
    public function getShippingAddressId(){
        return $this->_get(self::SHIPPING_ADDRESS_ID);
    }

    /**
     * Set shipping address id
     *
     * @param string $shippingAddressId
     *
     * @return $this
     */
    public function setShippingAddressId($shippingAddressId){
        return $this->setData(self::SHIPPING_ADDRESS_ID, $shippingAddressId);
    }

    /**
     * Get billing address id
     *
     * @return string|null
     */
    public function getBillingAddressId(){
        return $this->_get(self::BILLING_ADDRESS_ID);
    }

    /**
     * Set billing address id
     *
     * @param string $billingAddressId
     *
     * @return $this
     */
    public function setBillingAddressId($billingAddressId){
        return $this->setData(self::BILLING_ADDRESS_ID, $billingAddressId);
    }

    /**
     * Get purchase order file
     *
     * @return string|null
     */
    public function getPurchaseOrderFile(){
        return $this->_get(self::PURCHASE_ORDER_FILE);
    }

    /**
     * Set purchase order file
     *
     * @param string $purchaseOrderFile
     * @return $this
     */
    public function setPurchaseOrderFile($purchaseOrderFile){
        return $this->setData(self::PURCHASE_ORDER_FILE, $purchaseOrderFile);
    }

    /**
     * Get attach file
     *
     * @return string|null
     */
    public function getFilePath(){
        return $this->_get(self::FILE_PATH);
    }

    /**
     * Set attach file
     *
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath){
        return $this->setData(self::FILE_PATH, $filePath);
    }

    /**
     * Get download path
     * Its store download path
     *
     * @return string|null
     */
    public function getDownloadPath() {
        return $this->_get(self::DOWNLOAD_PATH);
    }

    /**
     * Set download path
     *
     * @param string $downloadPath
     * @return void
     */
    public function setDownloadPath($downloadPath) {
        // no need to store this value
        return;
    }
}
