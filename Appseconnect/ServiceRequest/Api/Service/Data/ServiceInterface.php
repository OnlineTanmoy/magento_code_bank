<?php

namespace Appseconnect\ServiceRequest\Api\Service\Data;

/**
 * Interface ServiceInterface
 * @package Appseconnect\ServiceRequest\Api\Service\Data
 */
interface ServiceInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
	 * Constants defined for keys of the data array. Identical to the name of the getter in snake case
	 */
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const MODEL_NUMBER = 'model_number';
    const PRODUCT_DESCRIPTION = 'product_description';
    const SERIAL_NUMBER = 'serial_number';
    const COPACK_SERIAL_NUMBER = 'copack_serial_number';
    const SHORT_DESCRIPTION ='short_description';
    const DETAILED_DESCRIPTION ='detailed_description';
    const SAFETY1 ='safety1';
    const SAFETY2 ='safety2';
    const SAFETY3 = 'safety3';
    const TERMS_CONDITIONS ='terms_condition';
    const STATUS ='status';
    const POST = 'post';

    const RA_ID = 'ra_id';
    const STORE_ID = 'store_id';
    const PURCHASE_ORDER_NUMBER = 'purchase_order_number';
    const PURCHASE_ORDER_FILE = 'purchase_order_file';
    const FILE_PATH = 'file_path';
    const DOWNLOAD_PATH = 'download_path';
    const IS_WARRANTY = 'is_warranty';
    const ORDER_ID = 'order_id';
    const DEVICE_TYPE = 'device_type';
    const CUSTOMER_NAME = 'customer_name';

    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const BILLING_ADDRESS_ID = 'billing_address_id';
    /**#@-*/

    /**
     * Get entity id
     *
     * @return int|null
     */
    public function getEntityId();
    
    /**
     * Set Entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

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
     * Get Model number
     *
     * @return string|null
     */
    public function getModelNumber();

    /**
     * Set Model Number
     *
     * @param string $modelNumber
     * @return $this
     */
    public function setModelNumber($modelNumber);

    /**
     * Get Product Description
     *
     * @return string|null
     */
    public function getProductDescription();

    /**
     * Set Product Description
     *
     * @param string $productDescription
     * @return $this
     */
    public function setProductDescription($productDescription);
    
    /**
     * Get serial number
     *
     * @return string|null
     */
    public function getSerialNumber();

    /**
     * Set serial number
     *
     * @param string $serialNumber
     * @return $this
     */
    public function setSerialNumber($serialNumber);
    
    /**
     * Get copack serial number
     *
     * @return string|null
     */
    public function getCopackSerialNumber();
    
    /**
     * Set copack serial number
     *
     * @param string $copackSerialNumber
     * @return $this
     */
    public function setCopackSerialNumber($copackSerialNumber);

    /**
     * Get short description
     *
     * @return string|null
     */
    public function getShortDescription();

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription);

    /**
     * Get detailed description
     *
     * @return string|null
     */
    public function getDetailedDescription();

    /**
     * Set detailed description
     *
     * @param string $detailedDescription
     * @return $this
     */
    public function setDetailedDescription($detailedDescription);

    /**
     * Get safety 1
     *
     * @return string|null
     */
    public function getSafety1();

    /**
     * Set safety 1
     *
     * @param string $safety1
     * @return $this
     */
    public function setSafety1($safety1);

    /**
     * Get safety 2
     *
     * @return string|null
     */
    public function getSafety2();

    /**
     * Set safety 2
     *
     * @param string $safety2
     * @return $this
     */
    public function setSafety2($safety2);

    /**
     * Get safety 3
     *
     * @return string|null
     */
    public function getSafety3();

    /**
     * Set safety 3
     *
     * @param string $safety3
     * @return $this
     */
    public function setSafety3($safety3);

    /**
     * Get terms condition
     *
     * @return string|null
     */
    public function getTermsCondition();

    /**
     * Set terms condition
     *
     * @param string $termsCondition
     * @return $this
     */
    public function setTermsCondition($termsCondition);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get post Date
     *
     * @return string|null
     */
    public function getPost();

    /**
     * Set Status
     *
     * @param string $post
     * @return $this
     */
    public function setPost($post);

    /**
     * Get Ra Id
     *
     * @return string|null
     */
    public function getRaId();

    /**
     * Set Ra Id
     *
     * @param string $raId
     * @return $this
     */
    public function setRaId($raId);

    /**
     * Get Store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set Store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get purchase order number
     *
     * @return string|null
     */
    public function getPurchaseOrderNumber();

    /**
     * Set purchase order number
     *
     * @param string $purchaseOrderNumber
     * @return $this
     */
    public function setPurchaseOrderNumber($purchaseOrderNumber);

    /**
     * Get is warranty
     *
     * @return int|null
     */
    public function getIsWarranty();

    /**
     * Set is warranty
     *
     * @param int $isWarranty
     * @return $this
     */
    public function setIsWarranty($isWarranty);

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get device type
     *
     * @return string|null
     */
    public function getDeviceType();

    /**
     * Set device type
     *
     * @param string $deviceType
     * @return $this
     */
    public function setDeviceType($deviceType);

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get shipping address id
     *
     * @return string|null
     */
    public function getShippingAddressId();

    /**
     * Set shipping address id
     *
     * @param string $shippingAddressId
     *
     * @return $this
     */
    public function setShippingAddressId($shippingAddressId);

    /**
     * Get billing address id
     *
     * @return string|null
     */
    public function getBillingAddressId();

    /**
     * Set billing address id
     *
     * @param string $billingAddressId
     *
     * @return $this
     */
    public function setBillingAddressId($billingAddressId);

    /**
     * Get purchase order file
     *
     * @return string|null
     */
    public function getPurchaseOrderFile();

    /**
     * Set purchase order file
     *
     * @param string $purchaseOrderFile
     * @return $this
     */
    public function setPurchaseOrderFile($purchaseOrderFile);

    /**
     * Get attach file
     *
     * @return string|null
     */
    public function getFilePath();

    /**
     * Set attach file
     *
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath);

    /**
     * Get download path
     *
     * @return string|null
     */
    public function getDownloadPath();

    /**
     * Set download path
     *
     * @param string $downloadPath
     * @return $this
     */
    public function setDownloadPath($downloadPath);
}
