<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\AvailableToPromise\Api\ProductInStock\Data;

/**
 * Interface ProductInStockInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductInStockInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * Available To Promise ID
     */
    const ID = 'id';

    /**
     * Available Date.
     */
    const AVAILABLE_DATE = 'available_date';

    /**
     * Product Sku.
     */
    const PRODUCT_SKU = 'product_sku';

    /**
     * Quantity.
     */
    const QUANTITY = 'quantity';

    /**
     * Available Quantity.
     */
    const AVAILABLE_QUANTITY = 'available_quantity';

    /**
     * Document Type.
     */
    const DOCUMENT_TYPE = 'document_type';

    /**
     * Warehouse.
     */
    const WAREHOUSE = 'warehouse';

    /**
     * Sync Flag.
     */
    const SYNC_FLAG = 'sync_flag';

    /**
     * Posting Date.
     */
    const POSTING_DATE = 'posting_date';
    /**
     * Items
     */
    const ITEMS = 'items';

    /**
     * Gets the id for Available To Promise.
     *
     * @return int|null Available To Promise Id.
     */
    public function getId();

    /**
     * Gets the website id for Available To Promise.
     *
     * @return string|null Available Date.
     */
    public function getAvailableDate();

    /**
     * Gets Product Sku.
     *
     * @return string|null Product Sku.
     */
    public function getProductSku();

    /**
     * Gets the Quantity.
     *
     * @return int Quantity.
     */
    public function getQuantity();

    /**
     * Gets the Available Quantity.
     *
     * @return int Available Quantity.
     */
    public function getAvailableQuantity();

    /**
     * Gets the Document Type.
     *
     * @return int Document Type.
     */
    public function getDocumentType();

    /**
     * Gets the Warehouse.
     *
     * @return int Warehouse.
     */
    public function getWarehouse();

    /**
     * Gets the Sync Flag.
     *
     * @return bool Sync Flag.
     */
    public function getSyncFlag();

    /**
     * Gets the Posting Date.
     *
     * @return string Posting Date.
     */
    public function getPostingDate();


    /**
     * Sets Available To Promise ID.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Sets Available Date.
     *
     * @param string $availableDate Available Date
     *
     * @return $this
     */
    public function setAvailableDate($availableDate);

    /**
     * Sets Product Sku.
     *
     * @param string $productSku Product Sku
     *
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Sets Quantity
     *
     * @param int $quantity Quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * Sets Available Quantity
     *
     * @param int $availableQuantity Available Quantity
     *
     * @return $this
     */
    public function setAvailableQuantity($availableQuantity);

    /**
     * Sets Document Type
     *
     * @param int $documentType Document Type
     *
     * @return $this
     */
    public function setDocumentType($documentType);

    /**
     * Sets Warehouse
     *
     * @param int $warehouse Warehouse
     *
     * @return $this
     */
    public function setWarehouse($warehouse);

    /**
     * Sets Sync Flag
     *
     * @param bool $syncFlag Sync Flag
     *
     * @return $this
     */
    public function setSyncFlag($syncFlag);

    /**
     * Sets Posting Date.
     *
     * @param string $postingDate Posting Date
     *
     * @return $this
     */
    public function setPostingDate($postingDate);


}
