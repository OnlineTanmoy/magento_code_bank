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
namespace Appseconnect\B2BMage\Api\Quotation\Data;

/**
 * Interface QuoteProductInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuoteProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * Id
     */
    const ID = "id";

    /**
     * Quote Id
     */
    const QUOTE_ID = "quote_id";
    
    /**
     * Super Attribute
     */
    const SUPER_ATTRIBUTE = "super_attribute";

    /**
     * Customer Id
     */
    const CUSTOMER_ID = "customer_id";

    /**
     * Product Id
     */
    const PRODUCT_ID = "product_id";

    /**
     * Product Sku
     */
    const PRODUCT_SKU = "product_sku";

    /**
     * Qty
     */
    const QTY = "qty";

    /**
     * Row Total
     */
    const ROW_TOTAL = "row_total";

    /**
     * Base Row Total
     */
    const BASE_ROW_TOTAL = "base_row_total";

    /**
     * Price
     */
    const PRICE = "price";

    /**
     * Base Price
     */
    const BASE_PRICE = "base_price";

    /**
     * Original Price
     */
    const ORIGINAL_PRICE = "original_price";

    /**
     * Base Original Price
     */
    const BASE_ORIGINAL_PRICE = "base_original_price";

    /**
     * Parent Item Id
     */
    const PARENT_ITEM_ID = "parent_item_id";

    /**
     * Weight
     */
    const WEIGHT = "weight";

    /**
     * Name
     */
    const NAME = "name";

    /**
     * Type
     */
    const PRODUCT_TYPE = "product_type";

    /**
     * Is Virtual
     */
    const IS_VIRTUAL = "is_virtual";
    
    /**
     * Store Id
     */
    const STORE_ID = "store_id";

    /**
     * Get Id .
     *
     * @return int|null Id.
     */
    public function getId();

    /**
     * Set Id .
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get Quote Id .
     *
     * @return int|null Id.
     */
    public function getQuoteId();

    /**
     * Set Quote Id .
     *
     * @param int $quoteId quote id
     *
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get Customer Id .
     *
     * @return int|null Id.
     */
    public function getCustomerId();

    /**
     * Set Customer Id .
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Product Id .
     *
     * @return int|null Id.
     */
    public function getProductId();

    /**
     * Set Product Id .
     *
     * @param int $productId product id
     *
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get Product Sku .
     *
     * @return string|null Id.
     */
    public function getProductSku();

    /**
     * Set Product Sku .
     *
     * @param string $productSku product sku
     *
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Get Qty .
     *
     * @return int|null Id.
     */
    public function getQty();

    /**
     * Set Qty .
     *
     * @param int $qty qty
     *
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get Row Total .
     *
     * @return float|null Id.
     */
    public function getRowTotal();

    /**
     * Set Row Total .
     *
     * @param float $rowTotal row total
     *
     * @return $this
     */
    public function setRowTotal($rowTotal);

    /**
     * Get Base Row Total .
     *
     * @return float|null Id.
     */
    public function getBaseRowTotal();

    /**
     * Set Base Row Total .
     *
     * @param float $baseRowTotal base row total
     *
     * @return $this
     */
    public function setBaseRowTotal($baseRowTotal);

    /**
     * Get Price .
     *
     * @return float|null Id.
     */
    public function getPrice();

    /**
     * Set Price .
     *
     * @param float $price price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get Base Price .
     *
     * @return float|null Id.
     */
    public function getBasePrice();

    /**
     * Set Base Price .
     *
     * @param float $basePrice base price
     *
     * @return $this
     */
    public function setBasePrice($basePrice);

    /**
     * Get Original Price .
     *
     * @return float|null Id.
     */
    public function getOriginalPrice();

    /**
     * Set Original Price .
     *
     * @param float $originalPrice original price
     *
     * @return $this
     */
    public function setOriginalPrice($originalPrice);

    /**
     * Get Base Original Price .
     *
     * @return float|null Id.
     */
    public function getBaseOriginalPrice();

    /**
     * Set Base Original Price .
     *
     * @param float $baseOriginalPrice base original price
     *
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice);

    /**
     * Get Parent Item Id .
     *
     * @return int|null Id.
     */
    public function getParentItemId();

    /**
     * Set Parent Item Id .
     *
     * @param int $parentItemId parent item id
     *
     * @return $this
     */
    public function setParentItemId($parentItemId);

    /**
     * Get Weight .
     *
     * @return string|null Id.
     */
    public function getWeight();

    /**
     * Set Weight .
     *
     * @param string $weight weight
     *
     * @return $this
     */
    public function setWeight($weight);

    /**
     * Get Name .
     *
     * @return string|null Id.
     */
    public function getName();

    /**
     * Set Name .
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Product Type .
     *
     * @return string|null Id.
     */
    public function getProductType();

    /**
     * Set Product Type .
     *
     * @param string $productType product type
     *
     * @return $this
     */
    public function setProductType($productType);

    /**
     * Get Is Virtual .
     *
     * @return int|null Id.
     */
    public function getIsVirtual();

    /**
     * Set Is Virtual .
     *
     * @param int $isVirtual is virtual
     *
     * @return $this
     */
    public function setIsVirtual($isVirtual);
    
    /**
     * Get Super Attribute .
     *
     * @return string|null Super Attribute.
     */
    public function getSuperAttribute();
    
    /**
     * Set Super Attribute .
     *
     * @param string $superAttribute super attribute
     *
     * @return $this
     */
    public function setSuperAttribute($superAttribute);
    
    /**
     * Get Store Id .
     *
     * @return int|null Id.
     */
    public function getStoreId();
    
    /**
     * Set Store Id .
     *
     * @param int $storeId store id
     *
     * @return $this
     */
    public function setStoreId($storeId);
}
