<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Magento\Catalog\Model\Product;
use Magento\Directory\Model\Currency;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Quote
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Quote extends \Magento\Framework\Model\AbstractModel implements QuoteInterface
{

    /**
     * Identifier for history item
     *
     * @var string
     */
    public $entityType = 'quotation';

    const ENTITY = 'quotation';

    /**
     * Quotation currency
     *
     * @var Currency
     */
    public $quotationCurrency = null;

    /**
     * Quote customer model object
     *
     * @var \Magento\Customer\Model\Customer
     */
    public $contact;

    /**
     * Total collector
     *
     * @var \Appseconnect\B2BMage\Model\Quote\TotalsCollector
     */
    public $totalsCollector;

    /**
     * Quote products collection
     *
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public $items;

    /**
     * Base currency
     *
     * @var Currency|null
     */
    public $baseCurrency = null;

    /**
     * Quote status
     *
     * @var \Appseconnect\B2BMage\Model\QuoteStatus
     */
    public $quoteStatus;

    /**
     * Quote item collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory
     */
    public $quoteItemCollectionFactory;
    
    /**
     * Object factory
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    public $objectFactory;
    
    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    public $catalogProduct;
    
    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public $timezone;
    
    /**
     * History collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory\CollectionFactory
     */
    public $historyCollectionFactory;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Quote interface
     *
     * @var \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterfaceFactory
     */
    public $quoteInterfaceFactory;
    
    /**
     * Data object helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    public $dataObjectHelper;
    
    /**
     * Quote history
     *
     * @var \Appseconnect\B2BMage\Model\QuoteHistoryFactory
     */
    public $quoteHistoryFactory;
    
    /**
     * Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $config;
    
    /**
     * Customer data
     *
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    public $customerDataFactory;
    
    /**
     * Customer repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;
    
    /**
     * Product proccessor
     *
     * @var \Appseconnect\B2BMage\Model\Quote\Product\Processor
     */
    public $productProcessor;
    
    /**
     * Stock registry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * Currency
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;
    
    /**
     * Product repository
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * Quote constructor.
     *
     * @param \Magento\Framework\Model\Context                               $context                    context
     * @param \Magento\Framework\DataObject\Factory                          $objectFactory              object
     * @param \Magento\Catalog\Helper\Product                                $catalogProduct             catalog product
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $timezone                   timezone
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterfaceFactory $quoteInterfaceFactory      quote interface
     * @param \Magento\Framework\Api\DataObjectHelper                        $dataObjectHelper           data object helper
     * @param QuoteHistoryFactory                                            $quoteHistoryFactory        quote history
     * @param ResourceModel\QuoteHistory\CollectionFactory                   $historyCollectionFactory   history collection
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                $helperContactPerson        contact person helper
     * @param Quote\TotalsCollector                                          $totalsCollector            total collector
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $config                     config
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory            $customerDataFactory        customer data
     * @param \Magento\Customer\Api\CustomerRepositoryInterface              $customerRepository         customer repository
     * @param Quote\Product\Processor                                        $productProcessor           product processor
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface           $stockRegistry              stock registry
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager               store manager
     * @param ProductRepositoryInterface                                     $productRepository          product repository
     * @param \Magento\Directory\Model\CurrencyFactory                       $currencyFactory            currency
     * @param QuoteStatus                                                    $quoteStatus                quote status
     * @param ResourceModel\QuoteProduct\CollectionFactory                   $quoteItemCollectionFactory quote item collection
     * @param \Magento\Framework\Registry                                    $registry                   registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null   $resource                   resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null             $resourceCollection         resource collection
     * @param array                                                          $data                       data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterfaceFactory $quoteInterfaceFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Appseconnect\B2BMage\Model\QuoteHistoryFactory $quoteHistoryFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory\CollectionFactory $historyCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Model\Quote\Product\Processor $productProcessor,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Appseconnect\B2BMage\Model\QuoteStatus $quoteStatus,
        \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        $this->quoteStatus = $quoteStatus;
        $this->objectFactory = $objectFactory;
        $this->catalogProduct = $catalogProduct;
        $this->timezone = $timezone;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->quoteInterfaceFactory = $quoteInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->totalsCollector = $totalsCollector;
        $this->quoteHistoryFactory = $quoteHistoryFactory;
        $this->config = $config;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerRepository = $customerRepository;
        $this->productProcessor = $productProcessor;
        $this->stockRegistry = $stockRegistry;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\Quote');
    }

    /**
     * Reset
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function reset()
    {
        $this->quotationCurrency = null;
        $this->_baseCurrency = null;
        
        return $this;
    }

    /**
     * Checks if it was set
     *
     * @return bool
     */
    public function itemsCollectionWasSet()
    {
        return null !== $this->items;
    }

    /**
     * Set status history
     *
     * @param array|null $statusHistories status history
     *
     * @return Quote
     */
    public function setStatusHistories(array $statusHistories = null)
    {
        return $this->setData(QuoteInterface::STATUS_HISTORIES, $statusHistories);
    }

    /**
     * Get status history
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteStatusHistoryInterface[]|null
     */
    public function getStatusHistories()
    {
        if ($this->getData(QuoteInterface::STATUS_HISTORIES) == null) {
            $this->setData(
                QuoteInterface::STATUS_HISTORIES,
                $this->getStatusHistoryCollection()
                    ->getItems()
            );
        }
        return $this->getData(QuoteInterface::STATUS_HISTORIES);
    }

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(QuoteInterface::ID);
    }

    /**
     * Set Id
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(QuoteInterface::ID, $id);
    }

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(QuoteInterface::CUSTOMER_ID);
    }

    /**
     * Set Customer Id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(QuoteInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Contact Id
     *
     * @return int|null
     */
    public function getContactId()
    {
        return $this->getData(QuoteInterface::CONTACT_ID);
    }

    /**
     * Set Contact Id
     *
     * @param int $contactId contact id
     *
     * @return $this
     */
    public function setContactId($contactId)
    {
        return $this->setData(QuoteInterface::CONTACT_ID, $contactId);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(QuoteInterface::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(QuoteInterface::STATUS, $status);
    }

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(QuoteInterface::STORE_ID);
    }

    /**
     * Set Store Id
     *
     * @param int $storeId store id
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(QuoteInterface::STORE_ID, $storeId);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(QuoteInterface::CREATED_AT);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt created at
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(QuoteInterface::CREATED_AT, $createdAt);
    }

    /**
     * Get Customer Name
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->getData(QuoteInterface::CUSTOMER_NAME);
    }

    /**
     * Set Customer Name
     *
     * @param string $customerName customer name
     *
     * @return $this
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(QuoteInterface::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get Contact Name
     *
     * @return string|null
     */
    public function getContactName()
    {
        return $this->getData(QuoteInterface::CONTACT_NAME);
    }

    /**
     * Set Contact Name
     *
     * @param string $contactName contact name
     *
     * @return $this
     */
    public function setContactName($contactName)
    {
        return $this->setData(QuoteInterface::CONTACT_NAME, $contactName);
    }

    /**
     * Get Subtotal
     *
     * @return float|null
     */
    public function getSubtotal()
    {
        return $this->getData(QuoteInterface::SUBTOTAL);
    }

    /**
     * Set Subtotal
     *
     * @param float $subtotal subtotal
     *
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        return $this->setData(QuoteInterface::SUBTOTAL, $subtotal);
    }

    /**
     * Get Grand Total
     *
     * @return float|null
     */
    public function getGrandTotal()
    {
        return $this->getData(QuoteInterface::GRAND_TOTAL);
    }

    /**
     * Set Grand Total
     *
     * @param float $grandTotal grandtotal
     *
     * @return $this
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(QuoteInterface::GRAND_TOTAL, $grandTotal);
    }

    /**
     * Get Grand Total Negotiated
     *
     * @return float|null
     */
    public function getGrandTotalNegotiated()
    {
        return $this->getData(QuoteInterface::GRAND_TOTAL_NEGOTIATED);
    }

    /**
     * Set Grand Total Negotiated
     *
     * @param float $grandTotalNegotiated grand total nagotiated
     *
     * @return $this
     */
    public function setGrandTotalNegotiated($grandTotalNegotiated)
    {
        return $this->setData(
            QuoteInterface::GRAND_TOTAL_NEGOTIATED,
            $grandTotalNegotiated
        );
    }

    /**
     * Get Customer Email
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->getData(QuoteInterface::CUSTOMER_EMAIL);
    }

    /**
     * Set Customer Email
     *
     * @param string $customerEmail customer email
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(QuoteInterface::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get Customer Group Id
     *
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        return $this->getData(QuoteInterface::CUSTOMER_GROUP_ID);
    }

    /**
     * Set Customer Group Id
     *
     * @param int $customerGroupId customer group id
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(
            QuoteInterface::CUSTOMER_GROUP_ID,
            $customerGroupId
        );
    }

    /**
     * Get Contact Email
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return $this->getData(QuoteInterface::CONTACT_EMAIL);
    }

    /**
     * Set Contact Email
     *
     * @param string $contactEmail contact email
     *
     * @return $this
     */
    public function setContactEmail($contactEmail)
    {
        return $this->setData(QuoteInterface::CONTACT_EMAIL, $contactEmail);
    }

    /**
     * Get Contact Group Id
     *
     * @return int|null
     */
    public function getContactGroupId()
    {
        return $this->getData(QuoteInterface::CONTACT_GROUP_ID);
    }

    /**
     * Set Contact Group Id
     *
     * @param int $contactGroupId contact group id
     *
     * @return $this
     */
    public function setContactGroupId($contactGroupId)
    {
        return $this->setData(
            QuoteInterface::CONTACT_GROUP_ID,
            $contactGroupId
        );
    }

    /**
     * Get Store Name
     *
     * @return string|null
     */
    public function getStoreName()
    {
        return $this->getData(QuoteInterface::STORE_NAME);
    }

    /**
     * Set Store Name
     *
     * @param string $storeName storename
     *
     * @return $this
     */
    public function setStoreName($storeName)
    {
        return $this->setData(QuoteInterface::STORE_NAME, $storeName);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(QuoteInterface::UPDATED_AT);
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt update at
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(QuoteInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Base Subtotal
     *
     * @return float|null
     */
    public function getBaseSubtotal()
    {
        return $this->getData(QuoteInterface::BASE_SUBTOTAL);
    }

    /**
     * Set Base Subtotal
     *
     * @param float $baseSubtotal base subtotal
     *
     * @return $this
     */
    public function setBaseSubtotal($baseSubtotal)
    {
        return $this->setData(QuoteInterface::BASE_SUBTOTAL, $baseSubtotal);
    }

    /**
     * Get Base Grand Total
     *
     * @return float|null
     */
    public function getBaseGrandTotal()
    {
        return $this->getData(QuoteInterface::BASE_GRAND_TOTAL);
    }

    /**
     * Set Base Grand Total
     *
     * @param float $baseGrandTotal base grand total
     *
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        return $this->setData(QuoteInterface::BASE_GRAND_TOTAL, $baseGrandTotal);
    }

    /**
     * Get Proposed Price
     *
     * @return float|null
     */
    public function getProposedPrice()
    {
        return $this->getData(QuoteInterface::PROPOSED_PRICE);
    }

    /**
     * Set Proposed Price
     *
     * @param float $proposedPrice proposed price
     *
     * @return $this
     */
    public function setProposedPrice($proposedPrice)
    {
        return $this->setData(QuoteInterface::PROPOSED_PRICE, $proposedPrice);
    }

    /**
     * Get Is Converted
     *
     * @return int|null
     */
    public function getIsConverted()
    {
        return $this->getData(QuoteInterface::IS_CONVERTED);
    }

    /**
     * Set Is Converted
     *
     * @param int $isConverted is converted
     *
     * @return $this
     */
    public function setIsConverted($isConverted)
    {
        return $this->setData(QuoteInterface::IS_CONVERTED, $isConverted);
    }

    /**
     * Get Items Qty
     *
     * @return int|null
     */
    public function getItemsQty()
    {
        return $this->getData(QuoteInterface::ITEMS_QTY);
    }

    /**
     * Set Items Qty
     *
     * @param int $itemsQty item qty
     *
     * @return $this
     */
    public function setItemsQty($itemsQty)
    {
        return $this->setData(QuoteInterface::ITEMS_QTY, $itemsQty);
    }

    /**
     * Get Items Count
     *
     * @return int|null
     */
    public function getItemsCount()
    {
        return $this->getData(QuoteInterface::ITEMS_COUNT);
    }

    /**
     * Set Items Count
     *
     * @param int $itemsCount item count
     *
     * @return $this
     */
    public function setItemsCount($itemsCount)
    {
        return $this->setData(QuoteInterface::ITEMS_COUNT, $itemsCount);
    }

    /**
     * Get Items
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface[]|null
     */
    public function getItems()
    {
        return $this->getData(QuoteInterface::ITEMS);
    }

    /**
     * Set items
     *
     * @param array|null $items items
     *
     * @return Quote
     */
    public function setItems(array $items = null)
    {
        return $this->setData(QuoteInterface::ITEMS, $items);
    }

    /**
     * Get Base Currency Code
     *
     * @return string|null
     */
    public function getBaseCurrencyCode()
    {
        return $this->getData(QuoteInterface::BASE_CURRENCY_CODE);
    }

    /**
     * Set Base Currency Code
     *
     * @param string $baseCurrencyCode base currency code
     *
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        return $this->setData(
            QuoteInterface::BASE_CURRENCY_CODE,
            $baseCurrencyCode
        );
    }

    /**
     * Get Store Currency Code
     *
     * @return string|null
     */
    public function getStoreCurrencyCode()
    {
        return $this->getData(QuoteInterface::STORE_CURRENCY_CODE);
    }

    /**
     * Set Store Currency Code
     *
     * @param string $storeCurrencyCode store currency code
     *
     * @return $this
     */
    public function setStoreCurrencyCode($storeCurrencyCode)
    {
        return $this->setData(QuoteInterface::STORE_CURRENCY_CODE, $storeCurrencyCode);
    }

    /**
     * Get Quotation Currency Code
     *
     * @return string|null
     */
    public function getQuotationCurrencyCode()
    {
        return $this->getData(QuoteInterface::QUOTATION_CURRENCY_CODE);
    }

    /**
     * Set Quotation Currency Code
     *
     * @param string $quotationCurrencyCode quotation currency code
     *
     * @return $this
     */
    public function setQuotationCurrencyCode($quotationCurrencyCode)
    {
        return $this->setData(
            QuoteInterface::QUOTATION_CURRENCY_CODE,
            $quotationCurrencyCode
        );
    }

    /**
     * Get Global Currency Code
     *
     * @return string|null
     */
    public function getGlobalCurrencyCode()
    {
        return $this->getData(QuoteInterface::GLOBAL_CURRENCY_CODE);
    }

    /**
     * Set Global Currency Code
     *
     * @param string $globalCurrencyCode global currency code
     *
     * @return $this
     */
    public function setGlobalCurrencyCode($globalCurrencyCode)
    {
        return $this->setData(
            QuoteInterface::GLOBAL_CURRENCY_CODE,
            $globalCurrencyCode
        );
    }

    /**
     * Get Is Active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData(QuoteInterface::IS_ACTIVE);
    }

    /**
     * Set Is Active
     *
     * @param int $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(QuoteInterface::IS_ACTIVE, $isActive);
    }

    /**
     * Get Customer Is Guest
     *
     * @return int|null
     */
    public function getCustomerIsGuest()
    {
        return $this->getData(QuoteInterface::CUSTOMER_IS_GUEST);
    }

    /**
     * Set Customer Is Guest
     *
     * @param int $customerIsGuest customer is guest
     *
     * @return $this
     */
    public function setCustomerIsGuest($customerIsGuest)
    {
        return $this->setData(
            QuoteInterface::CUSTOMER_IS_GUEST,
            $customerIsGuest
        );
    }

    /**
     * Get Customer Gender
     *
     * @return int|null
     */
    public function getCustomerGender()
    {
        return $this->getData(QuoteInterface::CUSTOMER_GENDER);
    }

    /**
     * Set Customer Gender
     *
     * @param int $customerGender customer gender
     *
     * @return $this
     */
    public function setCustomerGender($customerGender)
    {
        return $this->setData(QuoteInterface::CUSTOMER_GENDER, $customerGender);
    }

    /**
     * Get Increment Id
     *
     * @return string|null
     */
    public function getIncrementId()
    {
        return $this->getData(QuoteInterface::INCREMENT_ID);
    }

    /**
     * Set Increment Id
     *
     * @param string $incrementId increment id
     *
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(QuoteInterface::INCREMENT_ID, $incrementId);
    }

    /**
     * Get Base Proposed Price
     *
     * @return float|null
     */
    public function getBaseProposedPrice()
    {
        return $this->getData(QuoteInterface::BASE_PROPOSED_PRICE);
    }

    /**
     * Set Base Proposed Price
     *
     * @param float $baseProposedPrice base proposed price
     *
     * @return $this
     */
    public function setBaseProposedPrice($baseProposedPrice)
    {
        return $this->setData(
            QuoteInterface::BASE_PROPOSED_PRICE,
            $baseProposedPrice
        );
    }

    /**
     * Collect totals
     *
     * @return $this
     */
    public function collectTotals()
    {
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        
        $total = $this->totalsCollector->collect($this);
        $this->addData($total->getData());
        
        $this->setTotalsCollectedFlag(true);
        return $this;
    }

    /**
     * Set status label
     *
     * @return mixed
     */
    public function getStatusLabel()
    {
        return $this->quoteStatus->load($this->getStatus())
            ->getLabel();
    }

    /**
     * Is currency different
     *
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        return $this->getQuotationCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Get item collection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if ($this->hasItemsCollection()) {
            return $this->getData('items_collection');
        }
        if (null === $this->items) {
            $this->items = $this->quoteItemCollectionFactory->create();
            
            $this->items->setQuote($this);
        }
        return $this->items;
    }

    /**
     * Get formatted price value including order currency rate to quote website currency
     *
     * @param float $price       price
     * @param bool  $addBrackets add brackets
     *
     * @return string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * Format price precician
     *
     * @param float $price       price
     * @param int   $precision   precision
     * @param bool  $addBrackets add brackets
     *
     * @return string
     */
    public function formatPricePrecision(
        $price,
        $precision,
        $addBrackets = false
    ) {
    
        return $this->getQuotationCurrency()
            ->formatPrecision(
                $price,
                $precision,
                [],
                true,
                $addBrackets
            );
    }

    /**
     * Get currency model instance.
     * Will be used currency with which quote placed
     *
     * @return Currency
     */
    public function getQuotationCurrency()
    {
        if ($this->quotationCurrency === null) {
            $this->quotationCurrency = $this->currencyFactory->create();
            $this->quotationCurrency->load($this->getQuotationCurrencyCode());
        }
        return $this->quotationCurrency;
    }

    /**
     * Format base price precision
     *
     * @param float $price     price
     * @param int   $precision pricision
     *
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    /**
     * Retrieve quote website currency for working with base prices
     *
     * @return Currency
     */
    public function getBaseCurrency()
    {
        if ($this->baseCurrency === null) {
            $this->baseCurrency = $this->currencyFactory->create()
                ->load($this->getBaseCurrencyCode());
        }
        return $this->baseCurrency;
    }

    /**
     * Advanced func to add product to quote - processing mode can be specified there.
     * Returns error message if product type instance can't prepare product.
     *
     * @param mixed                                    $product     product
     * @param null|float|\Magento\Framework\DataObject $request     request
     * @param null|string                              $processMode proccess mode
     *
     * @return                                  \Magento\Quote\Model\Quote\Item|string
     * @throws                                  \Magento\Framework\Exception\LocalizedException @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addProductQuoteItem(
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
    
        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = $this->objectFactory->create(
                [
                'qty' => $request
                ]
            );
        }
        if (! $request instanceof \Magento\Framework\DataObject) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        
        $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced(
            $request,
            $product,
            $processMode
        );
        
        /**
         * Error message
         */
        if (is_string($cartCandidates) 
            || $cartCandidates instanceof \Magento\Framework\Phrase
        ) {
            return (string)$cartCandidates;
        }
        
        /**
         * If prepare process return one object
         */
        if (! is_array($cartCandidates)) {
            $cartCandidates = [
                $cartCandidates
            ];
        }
        
        $errors = [];
        $parentItem = $this->processItems($cartCandidates, $request);
        
        if (! empty($errors)) {
            throw new LocalizedException(
                __(implode("\n", $errors))
            );
        }
        return $parentItem;
    }
    
    /**
     * Proccess item
     *
     * @param mixed $cartCandidates cart candidate
     * @param mixed $request        request
     *
     * @return NULL|mixed
     */
    public function processItems($cartCandidates, $request)
    {
        $parentItem = null;
        $item = null;
        $items = [];
        foreach ($cartCandidates as $candidate) {
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            
            $candidate->setStickWithinParent($stickWithinParent);
            
            $item = $this->getItemByProduct($candidate);
            
            if (! $item) {
                $item = $this->productProcessor->init($candidate, $request);
                $this->addItem($item);
            }
            $items[] = $item;
            
            if (! $parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId() && ! $item->getParentItem()) {
                $item->setParentItem($parentItem);
            }
            
            $this->productProcessor->prepare($item, $request, $candidate);
        }
        return $parentItem;
    }

    /**
     * Get quote store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->storeManager->getStore($this->getStoreId());
    }

    /**
     * Declare quote store model
     *
     * @param \Magento\Store\Model\Store $store store
     *
     * @return $this
     */
    public function setStore(\Magento\Store\Model\Store $store)
    {
        $this->setStoreId($store->getId());
        return $this;
    }

    /**
     * Before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $globalCurrencyCode = $this->config->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            'default'
        );
        $baseCurrency = $this->getStore()->getBaseCurrency();
        
        $quoteCurrency = $this->getStore()->getCurrentCurrency();
        
        $this->setGlobalCurrencyCode($globalCurrencyCode);
        $this->setBaseCurrencyCode($baseCurrency->getCode());
        $this->setStoreCurrencyCode($baseCurrency->getCode());
        $this->setQuotationCurrencyCode($quoteCurrency->getCode());
        if ($this->contact) {
            $this->setContactId($this->contact->getId());
        }
        
        parent::beforeSave();
    }

    /**
     * Retrieve quote items array
     *
     * @return array
     */
    public function getAllItems()
    {
        $items = [];
        
        foreach ($this->getItemsCollection() as $item) {

            if (! $item->isDeleted()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param \Magento\Catalog\Model\Product $product product
     *
     * @return \Appseconnect\B2BMage\Model\QuoteProduct|bool
     */
    public function getItemByProduct($product)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Get all vissble item
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (! $item->isDeleted() && ! $item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Get item by id
     *
     * @param $itemId item id
     *
     * @return mixed
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * Remove item
     *
     * @param $itemId item id
     *
     * @return $this
     */
    public function removeItem($itemId)
    {
        $item = $this->getItemById($itemId);
        
        if ($item) {
            $item->setQuote($this);
            $item->isDeleted(true);
            
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }
            
            $parent = $item->getParentItem();
            if ($parent) {
                $parent->isDeleted(true);
            }
            
            if (! $this->getAllVisibleItems()) {
                $this->isDeleted(true);
            }
        }
        
        return $this;
    }

    /**
     * Load by contect
     *
     * @param $contact contact
     *
     * @return $this
     */
    public function loadByContact($contact)
    {
        if ($contact instanceof \Magento\Customer\Model\Customer 
            || $contact instanceof CustomerInterface
        ) {
            $contactId = $contact->getId();
        } else {
            $contactId = (int) $contact;
        }
        $this->_getResource()->loadByContactId($this, $contactId);
        return $this;
    }

    /**
     * Set customer
     *
     * @param CustomerInterface|null $contact contact
     *
     * @return $this
     */
    public function setCustomer(
        \Magento\Customer\Api\Data\CustomerInterface $contact = null
    ) {
    
        if ($contact->getCustomAttribute('customer_type')->getValue() == 3) {
            $customerMap = $this->helperContactPerson->getCustomerId($contact->getId());
            $customer = $this->customerRepository->getById($customerMap['customer_id']);
        }
        $this->contact = $contact;
        $contactName = $contact->getMiddleName() ?
                       $contact->getFirstName() . ' ' .
                       $contact->getMiddleName() . ' ' .
                       $contact->getLastName() : $contact->getFirstName() . ' ' .
                       $contact->getLastName();
        
        $this->setContactId($contact->getId());
        $this->setContactGroupId($contact->getGroupId());
        $this->setContactEmail($contact->getEmail());
        $this->setContactName($contactName);
        if ($customer) {
            $customerName = $customer->getMiddleName() ?
            $customer->getFirstName() . ' ' .
            $customer->getMiddleName() . ' ' .
            $customer->getLastName() : $customer->getFirstName() . ' ' .
            $customer->getLastName();
            $this->setCustomerId($customer->getId());
            $this->setCustomerGroupId($customer->getGroupId());
            $this->setCustomerEmail($customer->getEmail());
            $this->setCustomerName($customerName);
        }
        
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (null === $this->contact) {
            try {
                $this->contact = $this->customerRepository->getById(
                    $this->getCustomerId()
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->contact = $this->customerDataFactory->create();
                $this->contact->setId(null);
            }
        }
        
        return $this->contact;
    }

    /**
     * Adding new product to quote
     *
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $item item
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItem(\Appseconnect\B2BMage\Model\QuoteProduct $item)
    {
        $item->setQuote($this);
        if (! $item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Add a comment to quote
     * Different or default status may be specified
     *
     * @param string      $comment comment
     * @param bool|string $status  status
     *
     * @return QuoteStatusHistoryInterface
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } else {
            $this->setStatus($status);
        }
        $history = $this->quoteHistoryFactory->create()
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->entityType);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     * Add status history
     *
     * @param QuoteHistory $history history
     *
     * @return $this
     */
    public function addStatusHistory(\Appseconnect\B2BMage\Model\QuoteHistory $history)
    {
        $history->setQuote($this);
        $this->setStatus($history->getStatus());
        if (! $history->getId()) {
            $this->setStatusHistories(
                array_merge(
                    $this->getStatusHistories(), [
                    $history
                    ]
                )
            );
            $this->setDataChanges(true);
        }
        return $this;
    }

    /**
     * Get status history collection
     *
     * @return mixed
     */
    public function getStatusHistoryCollection()
    {
        $collection = $this->historyCollectionFactory->create()
            ->setQuoteFilter($this)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        if ($this->getId()) {
            foreach ($collection as $status) {
                $status->setOrder($this);
            }
        }
        return $collection;
    }

    /**
     * Get visible status history
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (! $status->isDeleted() 
                && $status->getComment() 
                && $status->getIsVisibleOnFront()
            ) {
                $history[] = $status;
            }
        }
        return $history;
    }

    /**
     * Get data model
     *
     * @return mixed
     */
    public function getDataModel()
    {
        $quoteData = $this->getData();
        $quoteDataObject = $this->quoteInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteDataObject,
            $quoteData,
            '\Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface'
        );
        $quoteDataObject->setId($this->getId());
        return $quoteDataObject;
    }

    /**
     * Update item
     *
     * @param $itemId     item id
     * @param $buyRequest buy request
     * @param null $params     params
     *
     * @return QuoteProduct|bool|\Magento\Quote\Model\Quote\Item|string|NULL
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItemById($itemId);
        if (! $item) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This is the wrong quote item id to update configuration.')
            );
        }
        $productId = $item->getProduct()->getId();
        
        $product = clone $this->productRepository->getById(
            $productId,
            false,
            $this->getStore()
                ->getId()
        );
        
        if (! $params) {
            $params = $this->objectFactory->create();
        } elseif (is_array($params)) {
            $params = $this->objectFactory->create($params);
        }
        $params->setCurrentConfig($item->getBuyRequest());
        $buyRequest = $this->catalogProduct->addParamsToBuyRequest(
            $buyRequest,
            $params
        );
        
        $outputItem = $this->addProductQuoteItem($product, $buyRequest);
        
        if (is_string($outputItem)) {
            throw new LocalizedException(__($outputItem));
        }
        
        if ($outputItem->getParentItem()) {
            $outputItem = $outputItem->getParentItem();
        }
        
        if ($outputItem->getId() != $itemId) {
            /**
             * Product configuration didn't stick to original quote item
             * It either has same configuration as some other quote item's product or completely new configuration
             */
            $this->removeItem($itemId);
            $items = $this->getAllItems();
            foreach ($items as $item) {
                if ($item->getProductId() == $productId 
                    && $item->getId() != $outputItem->getId()
                ) {
                    if ($outputItem->compare($item)) {
                        $outputItem->setQty($outputItem->getQty() + $item->getQty());
                        $this->removeItem($item->getId());
                        break;
                    }
                }
            }
        } else {
            $outputItem->setQty($buyRequest->getQty());
        }
        
        return $outputItem;
    }

    /**
     * Has comments
     *
     * @return mixed
     */
    public function hasComments()
    {
        $size = $this->getStatusHistoryCollection()->getSize();
        return $size;
    }

    /**
     * Get formatted quote created date in store timezone
     *
     * @param string $format format
     *
     * @return string
     */
    public function getCreatedAtFormatted($format)
    {
        $createdAt = $this->timezone->date($this->getCreatedAt());
        return $this->timezone->formatDateTime(
            $createdAt,
            $format,
            $format,
            null,
            $this->timezone->getConfigTimezone('store', $this->getStore())
        );
    }

    /**
     * Can submit
     *
     * @return bool
     */
    public function canSubmit()
    {
        return ($this->getId() && $this->getStatus() == 'open') ? true : false;
    }

    /**
     * Submit
     *
     * @return bool
     */
    public function submit()
    {
        if ($this->canSubmit()) {
            $this->setStatus('submitted');
            return true;
        }
        return false;
    }

    /**
     * Can cancel
     *
     * @return bool
     */
    public function canCancel()
    {
        return ($this->getId() && $this->getStatus() == 'submitted') ? true : false;
    }

    /**
     * Cancel
     *
     * @return bool
     */
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->setStatus('canceled');
            return true;
        }
        return false;
    }

    /**
     * Can hold
     *
     * @return bool
     */
    public function canHold()
    {
        return ($this->getId() && $this->getStatus() == 'submitted') ? true : false;
    }

    /**
     * Hold
     *
     * @return bool
     */
    public function hold()
    {
        if ($this->canHold()) {
            $this->setStatus('holded');
            return true;
        }
        return false;
    }

    /**
     * Can unhold
     *
     * @return bool
     */
    public function canUnhold()
    {
        return ($this->getId() && $this->getStatus() == 'holded') ? true : false;
    }

    /**
     * Unhold
     *
     * @return bool
     */
    public function unhold()
    {
        if ($this->canUnhold()) {
            $this->setStatus('submitted');
            return true;
        }
        return false;
    }

    /**
     * Can Approve
     *
     * @return bool
     */
    public function canApprove()
    {
        return ($this->getId() && $this->getStatus() == 'submitted') ? true : false;
    }

    /**
     * Approve
     *
     * @return bool
     */
    public function approve()
    {
        if ($this->canApprove()) {
            $this->setStatus('approved');
            return true;
        }
        return false;
    }
}
