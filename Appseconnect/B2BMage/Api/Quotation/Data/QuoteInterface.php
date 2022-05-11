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
 * Interface QuoteInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuoteInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * Id
     */
    const ID = "id";

    /**
     * Customer Id
     */
    const CUSTOMER_ID = "customer_id";

    /**
     * Contact Id
     */
    const CONTACT_ID = "contact_id";

    /**
     * Status
     */
    const STATUS = "status";

    /**
     * Items
     */
    const ITEMS = "items";

    /**
     * Store Id
     */
    const STORE_ID = "store_id";

    /**
     * Created At
     */
    const CREATED_AT = "created_at";

    /**
     * Customer Name
     */
    const CUSTOMER_NAME = "customer_name";

    /**
     * Contact Name
     */
    const CONTACT_NAME = "contact_name";

    /**
     * Subtotal
     */
    const SUBTOTAL = "subtotal";

    /**
     * Grand Total
     */
    const GRAND_TOTAL = "grand_total";

    /**
     * Grand Total Negotiated
     */
    const GRAND_TOTAL_NEGOTIATED = "grand_total_negotiated";

    /**
     * Customer Email
     */
    const CUSTOMER_EMAIL = "customer_email";

    /**
     * Customer Group Id
     */
    const CUSTOMER_GROUP_ID = "customer_group_id";

    /**
     * Contact Email
     */
    const CONTACT_EMAIL = "contact_email";

    /**
     * Contact Group Id
     */
    const CONTACT_GROUP_ID = "contact_group_id";

    /**
     * Store Name
     */
    const STORE_NAME = "store_name";

    /**
     * Updated At
     */
    const UPDATED_AT = "updated_at";

    /**
     * Base Subtotal
     */
    const BASE_SUBTOTAL = "base_subtotal";

    /**
     * Base Grand Total
     */
    const BASE_GRAND_TOTAL = "base_grand_total";

    /**
     * Proposed Price
     */
    const PROPOSED_PRICE = "proposed_price";

    /**
     * Is Converted
     */
    const IS_CONVERTED = "is_converted";

    /**
     * Items Qty
     */
    const ITEMS_QTY = "items_qty";

    /**
     * Items Count
     */
    const ITEMS_COUNT = "items_count";

    /**
     * Base Currency Code
     */
    const BASE_CURRENCY_CODE = "base_currency_code";

    /**
     * Store Currency Code
     */
    const STORE_CURRENCY_CODE = "store_currency_code";

    /**
     * Quotation Currency Code
     */
    const QUOTATION_CURRENCY_CODE = "quotation_currency_code";

    /**
     * Global Currency Code
     */
    const GLOBAL_CURRENCY_CODE = "global_currency_code";

    /**
     * Is Active
     */
    const IS_ACTIVE = "is_active";

    /**
     * Customer Is Guest
     */
    const CUSTOMER_IS_GUEST = "customer_is_guest";

    /**
     * Customer Gender
     */
    const CUSTOMER_GENDER = "customer_gender";

    /**
     * Increment Id
     */
    const INCREMENT_ID = "increment_id";

    /**
     * Base Proposed Price
     */
    const BASE_PROPOSED_PRICE = "base_proposed_price";

    /**
     * Status histories.
     */
    const STATUS_HISTORIES = 'status_histories';

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
     * Get Contact Id .
     *
     * @return int|null Id.
     */
    public function getContactId();

    /**
     * Set Contact Id .
     *
     * @param int $contactId contact id
     *
     * @return $this
     */
    public function setContactId($contactId);

    /**
     * Get Status .
     *
     * @return string|null Id.
     */
    public function getStatus();

    /**
     * Set Status .
     *
     * @param string $status status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Lists items in the cart.
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface[]|null Array of items. Otherwise, null.
     */
    public function getItems();

    /**
     * Sets items in the cart.
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface[] $items items
     *
     * @return $this
     */
    public function setItems(array $items = null);

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

    /**
     * Get Created At .
     *
     * @return string|null Id.
     */
    public function getCreatedAt();

    /**
     * Set Created At .
     *
     * @param string $createdAt create at
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Customer Name .
     *
     * @return string|null Id.
     */
    public function getCustomerName();

    /**
     * Set Customer Name .
     *
     * @param string $customerName customer name
     *
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get Contact Name .
     *
     * @return string|null Id.
     */
    public function getContactName();

    /**
     * Set Contact Name .
     *
     * @param string $contactName contact name
     *
     * @return $this
     */
    public function setContactName($contactName);

    /**
     * Get Subtotal .
     *
     * @return float|null Id.
     */
    public function getSubtotal();

    /**
     * Set Subtotal .
     *
     * @param float $subtotal subtotal
     *
     * @return $this
     */
    public function setSubtotal($subtotal);

    /**
     * Get Grand Total .
     *
     * @return float|null Id.
     */
    public function getGrandTotal();

    /**
     * Set Grand Total .
     *
     * @param float $grandTotal grand total
     *
     * @return $this
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get Grand Total Negotiated .
     *
     * @return float|null Id.
     */
    public function getGrandTotalNegotiated();

    /**
     * Set Grand Total Negotiated .
     *
     * @param float $grandTotalNegotiated grand total negotiated
     *
     * @return $this
     */
    public function setGrandTotalNegotiated($grandTotalNegotiated);

    /**
     * Get Customer Email .
     *
     * @return string|null Id.
     */
    public function getCustomerEmail();

    /**
     * Set Customer Email .
     *
     * @param string $customerEmail customer email
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get Customer Group Id .
     *
     * @return int|null Id.
     */
    public function getCustomerGroupId();

    /**
     * Set Customer Group Id .
     *
     * @param int $customerGroupId customer group id
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Get Contact Email .
     *
     * @return string|null Id.
     */
    public function getContactEmail();

    /**
     * Set Contact Email .
     *
     * @param string $contactEmail contact email
     *
     * @return $this
     */
    public function setContactEmail($contactEmail);

    /**
     * Get Contact Group Id .
     *
     * @return int|null Id.
     */
    public function getContactGroupId();

    /**
     * Set Contact Group Id .
     *
     * @param int $contactGroupId contact group id
     *
     * @return $this
     */
    public function setContactGroupId($contactGroupId);

    /**
     * Get Store Name .
     *
     * @return string|null Id.
     */
    public function getStoreName();

    /**
     * Set Store Name .
     *
     * @param string $storeName store name
     *
     * @return $this
     */
    public function setStoreName($storeName);

    /**
     * Get Updated At .
     *
     * @return string|null Id.
     */
    public function getUpdatedAt();

    /**
     * Set Updated At .
     *
     * @param string $updatedAt updated at
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Base Subtotal .
     *
     * @return float|null Id.
     */
    public function getBaseSubtotal();

    /**
     * Set Base Subtotal .
     *
     * @param float $baseSubtotal base subtotal
     *
     * @return $this
     */
    public function setBaseSubtotal($baseSubtotal);

    /**
     * Get Base Grand Total .
     *
     * @return float|null Id.
     */
    public function getBaseGrandTotal();

    /**
     * Set Base Grand Total .
     *
     * @param float $baseGrandTotal base grandtotal
     *
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal);

    /**
     * Get Proposed Price .
     *
     * @return float|null Id.
     */
    public function getProposedPrice();

    /**
     * Set Proposed Price .
     *
     * @param float $proposedPrice proposed price
     *
     * @return $this
     */
    public function setProposedPrice($proposedPrice);

    /**
     * Get Is Converted .
     *
     * @return int|null Id.
     */
    public function getIsConverted();

    /**
     * Set Is Converted .
     *
     * @param int $isConverted is converted
     *
     * @return $this
     */
    public function setIsConverted($isConverted);

    /**
     * Get Items Qty .
     *
     * @return int|null Id.
     */
    public function getItemsQty();

    /**
     * Set Items Qty .
     *
     * @param int $itemsQty items qty
     *
     * @return $this
     */
    public function setItemsQty($itemsQty);

    /**
     * Get Items Count .
     *
     * @return int|null Id.
     */
    public function getItemsCount();

    /**
     * Set Items Count .
     *
     * @param int $itemsCount items count
     *
     * @return $this
     */
    public function setItemsCount($itemsCount);

    /**
     * Get Base Currency Code .
     *
     * @return string|null Id.
     */
    public function getBaseCurrencyCode();

    /**
     * Set Base Currency Code .
     *
     * @param string $baseCurrencyCode base currency code
     *
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode);

    /**
     * Get Store Currency Code .
     *
     * @return string|null Id.
     */
    public function getStoreCurrencyCode();

    /**
     * Set Store Currency Code .
     *
     * @param string $storeCurrencyCode store currency code
     *
     * @return $this
     */
    public function setStoreCurrencyCode($storeCurrencyCode);

    /**
     * Get Quotation Currency Code .
     *
     * @return string|null Id.
     */
    public function getQuotationCurrencyCode();

    /**
     * Set Quotation Currency Code .
     *
     * @param string $quotationCurrencyCode quotation currency code
     *
     * @return $this
     */
    public function setQuotationCurrencyCode($quotationCurrencyCode);

    /**
     * Get Global Currency Code .
     *
     * @return string|null Id.
     */
    public function getGlobalCurrencyCode();

    /**
     * Set Global Currency Code .
     *
     * @param string $globalCurrencyCode global currency code
     *
     * @return $this
     */
    public function setGlobalCurrencyCode($globalCurrencyCode);

    /**
     * Get Is Active .
     *
     * @return int|null Id.
     */
    public function getIsActive();

    /**
     * Set Is Active .
     *
     * @param int $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get Customer Is Guest .
     *
     * @return int|null Id.
     */
    public function getCustomerIsGuest();

    /**
     * Set Customer Is Guest .
     *
     * @param int $customerIsGuest customer is guest
     *
     * @return $this
     */
    public function setCustomerIsGuest($customerIsGuest);

    /**
     * Get Customer Gender .
     *
     * @return int|null Id.
     */
    public function getCustomerGender();

    /**
     * Set Customer Gender .
     *
     * @param int $customerGender customer gender
     *
     * @return $this
     */
    public function setCustomerGender($customerGender);

    /**
     * Get Increment Id .
     *
     * @return string|null Id.
     */
    public function getIncrementId();

    /**
     * Set Increment Id .
     *
     * @param string $incrementId increment id
     *
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * Get Base Proposed Price .
     *
     * @return float|null Id.
     */
    public function getBaseProposedPrice();

    /**
     * Set Base Proposed Price .
     *
     * @param float $baseProposedPrice base proposed price
     *
     * @return $this
     */
    public function setBaseProposedPrice($baseProposedPrice);

    /**
     * Gets status histories for the quote.
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteStatusHistoryInterface[]|null Array of status histories.
     */
    public function getStatusHistories();

    /**
     * Sets status histories for the quote.
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteStatusHistoryInterface[] $statusHistories status history
     * 
     * @return $this
     */
    public function setStatusHistories(array $statusHistories = null);
}
