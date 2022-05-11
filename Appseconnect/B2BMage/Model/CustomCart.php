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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject;

/**
 * Class CustomCart
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomCart extends DataObject
{

    /**
     * Quote id
     *
     * @var int
     */
    public $quoteId;
    
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;
    
    /**
     * Object
     *
     * @var DataObject\Factory
     */
    public $objectFactory;
    
    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Customer Repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;
    
    /**
     * Quote
     *
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    public $quoteFactory;
    
    /**
     * Stock state
     *
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    public $stockState;
    
    /**
     * Quotation repository
     *
     * @var \Appseconnect\B2BMage\Model\QuotationRepository
     */
    public $quotationRepository;
    
    /**
     * Product proccesser
     *
     * @var \Appseconnect\B2BMage\Model\Quote\Product\Processor
     */
    public $productProcessor;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;
    
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
     * Currency factory
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;
    
    /**
     * Quote item collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory
     */
    public $quoteItemCollectionFactory;
    
    /**
     * Product repository
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * CustomCart constructor.
     *
     * @param DataObject\Factory                                   $objectFactory              object factory
     * @param Session                                              $customerSession            customer session
     * @param \Magento\Customer\Api\CustomerRepositoryInterface    $customerRepository         customer repository
     * @param QuoteFactory                                         $quoteFactory               quote
     * @param \Magento\CatalogInventory\Api\StockStateInterface    $stockState                 stock state
     * @param QuotationRepository                                  $quotationRepository        quotation repository
     * @param Quote\Product\Processor                              $productProcessor           product proccesser
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry              stock registry
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager               store manager
     * @param ProductRepositoryInterface                           $productRepository          product repository
     * @param \Magento\Directory\Model\CurrencyFactory             $currencyFactory            currency factory
     * @param ResourceModel\QuoteProduct\CollectionFactory         $quoteItemCollectionFactory quote item collection
     * @param \Magento\Framework\Registry                          $registry                   registry
     * @param array                                                $data                       data
     */
    public function __construct(
        DataObject\Factory $objectFactory,
        Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository,
        \Appseconnect\B2BMage\Model\Quote\Product\Processor $productProcessor,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
    
        $this->objectFactory = $objectFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->quoteFactory = $quoteFactory;
        $this->stockState = $stockState;
        $this->quotationRepository = $quotationRepository;
        $this->productProcessor = $productProcessor;
        $this->coreRegistry = $registry;
        $this->stockRegistry = $stockRegistry;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct($data);
    }
    
    /**
     * Get catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->create(\Magento\Catalog\Model\Session::class);
        return $this->catalogSession;
    }

    /**
     * Get quote object associated with custom cart.
     * By default it is current customer session quote
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        $salesrepId = $this->getCatalogSession()->getSalesrepId();
        if (! $this->hasData('quote')) {
            try {
                if ($salesrepId && $this->quoteId) {
                    $quote = $this->quotationRepository->get($this->quoteId);
                } else {
                    $quote = $this->quotationRepository->getForContact(
                        $this->customerSession->getCustomer()
                            ->getId()
                    );
                }
                
                $this->setData('quote', $quote);
            } catch (\Exception $e) {
                $customer = $this->customerRepository->getById(
                    $this->customerSession->getCustomer()
                        ->getId()
                );

                $quote = $this->quoteFactory->create();
                $quote->setCreatedAt(date("Y-m-d H:i:s"));
                $quote->setStatus('open');
                $quote->setStore($this->storeManager->getStore());
                $quote->setCustomer($customer);
                $quote->setCustomerIsGuest(0);
                $this->quotationRepository->save($quote);
                $this->setData('quote', $quote);
            }
        }
        return $this->_getData('quote');
    }

    /**
     * Set quote object associated with the custom cart
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote quote
     *
     * @return $this @codeCoverageIgnore
     */
    public function setQuote(\Appseconnect\B2BMage\Model\Quote $quote)
    {
        $this->setData('quote', $quote);
        return $this;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product                             $productInfo product info
     * @param \Magento\Framework\DataObject|int|array $requestInfo request info
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addQuoteProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);
        $productId = $product->getId();
        
        if ($productId) {
            $stockItem = $this->stockRegistry->getStockItem(
                $productId, $product->getStore()
                    ->getWebsiteId()
            );
            $minimumQty = $stockItem->getMinSaleQty();
            if ($minimumQty 
                && $minimumQty > 0 
                && ! $request->getQty() 
                && ! $this->getQuote()->hasProductId($productId)
            ) {
                $request->setQty($minimumQty);
            }
        }
        
        if ($productId) {
            try {
                $result = $this->getQuote()->addProductQuoteItem($product, $request);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result = $e->getMessage();
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The product does not exist.'));
        }
        return $this;
    }

    /**
     * Save cart
     *
     * @return $this
     */
    public function save()
    {
        $this->getQuote()->setTotalsCollectedFlag(false);
        $this->quotationRepository->save($this->getQuote());
        return $this;
    }

    /**
     * Get product object based on requested product information
     *
     * @param Product|int|string $productInfo product info
     *
     * @return Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (! $product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
            }
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'), $e);
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
        }
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        if (! is_array($product->getWebsiteIds()) 
            || ! in_array($currentWebsiteId, $product->getWebsiteIds())
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t find the product.')
            );
        }
        return $product;
    }

    /**
     * Suggest item qty
     *
     * @param mixed $data    data
     * @param int   $quoteId quote id
     *
     * @return array
     */
    public function suggestItemsQty($data, $quoteId = null)
    {
        if ($quoteId) {
            $this->quoteId = $quoteId;
        }
        
        foreach ($data as $itemId => $itemInfo) {
            if (! isset($itemInfo['qty'])) {
                continue;
            }
            $qty = (float) $itemInfo['qty'];
            if ($qty <= 0) {
                continue;
            }
            
            $quoteItem = $this->getQuote()->getItemById($itemId);
            if (! $quoteItem) {
                continue;
            }
            
            $product = $quoteItem->getProduct();
            if (! $product) {
                continue;
            }
            
            $data[$itemId]['before_suggest_qty'] = $qty;
            $data[$itemId]['qty'] = $this->stockState->suggestQty(
                $product->getId(),
                $qty,
                $product->getStore()
                    ->getWebsiteId()
            );
        }
        return $data;
    }

    /**
     * Update Items
     *
     * @param mixed $data data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Appseconnect\B2BMage\Model\CustomCart
     */
    public function updateItems($data)
    {
        $infoDataObject = $this->objectFactory->create($data);
        
        $qtyRecalculatedFlag = false;
        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (! $item) {
                continue;
            }
            
            $qty = isset($itemInfo['qty']) ? (double) $itemInfo['qty'] : false;
            $price = isset($itemInfo['price']) ? (double) $itemInfo['price'] : false;
            if ($qty > 0) {
                $item->setQty($qty);
                
                if ($item->getHasError()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                }
                
                if (isset($itemInfo['before_suggest_qty']) && $itemInfo['before_suggest_qty'] != $qty) {
                    $qtyRecalculatedFlag = true;
                    $this->messageManager->addNotice(
                        __(
                            'Quantity was recalculated from %1 to %2',
                            $itemInfo['before_suggest_qty'],
                            $qty
                        ),
                        'quote_item' . $item->getId()
                    );
                }
            }
            
            if ($price > 0) {
                $item->setPrice($price);
                $item->setBasePrice($price);
                
                if ($item->getHasError()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                }
            }
        }
        
        if ($qtyRecalculatedFlag) {
            $this->messageManager->addNotice(
                __('We adjusted product quantities to fit the required increments.')
            );
        }
        
        return $this;
    }

    /**
     * Remove item
     *
     * @param int $itemId item id
     *
     * @return \Appseconnect\B2BMage\Model\CustomCart
     */
    public function removeItem($itemId)
    {
        $this->getQuote()->removeItem($itemId);
        return $this;
    }

    /**
     * Truncate
     *
     * @return \Appseconnect\B2BMage\Model\CustomCart
     */
    public function truncate()
    {
        $this->getQuote()->isDeleted(true);
        return $this;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param \Magento\Framework\DataObject|int|array $requestInfo request info
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = $this->objectFactory->create(
                [
                'qty' => $requestInfo
                ]
            );
        } elseif (is_array($requestInfo)) {
            $request = $this->objectFactory->create($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        
        return $request;
    }
}
