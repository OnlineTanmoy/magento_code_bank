<?php

namespace Appseconnect\B2BMage\Plugin\Cart;

use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

class CartPlugin
{
    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;


    public $storeManager;

    public $productRepository;

    public $cart;

    public $serializer;

    /**
     * @var QuoteItemFactory
     */
    public $quoteItemFactory;

    /**
     * @var ItemFactory
     */
    public $itemFactory;

    public $mathRandom;

    public $quoteRepository;

    public $request;
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data
     */
    public $helperCustomerSpecialPrice;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;

    /**
     * ProductFactory
     *
     * @var Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryDiscount\Data
     */
    public $helperCategory;
    public $divisionHelper;
    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;
    /**
     * PriceCurrencyInterface
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrencyInterface;

    /**
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    public $quotationHelper;

    public function __construct
    (
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Cart $cart,
        SerializerInterface $serializer,
        QuoteItemFactory $quoteItemFactory,
        ItemFactory $itemFactory,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper

    ) {
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->serializer = $serializer;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemFactory = $itemFactory;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperTierprice = $helperTierprice;
        $this->helperPricelist = $helperPricelist;
        $this->helperPriceRule = $helperPriceRule;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->helperCategory = $helperCategory;
        $this->divisionHelper = $divisionHelper;
        $this->quotationHelper = $quotationHelper;

    }

    /**
     * afterRemoveItem
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $result
     * @param $itemId
     */
    public function afterRemoveItem(\Magento\Checkout\Model\Cart $subject, $result, $itemId)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $websiteId = $this->customerSession->getCustomer()->getWebsiteId();
        if ($customerId) {
            if ($customerType == 2) {
                return $this;
            }
            if ($customerType != 3) {
                return $this;
            }
            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
                $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
                $customerPricelistCode = $customerCollection->getData('pricelist_code');
                $customerId = $customerDetail['customer_id'];
            } else {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
                $mainCustomerId = $this->divisionHelper->getMainCustomerId($customerDetail['customer_id']);

                $customerCollection = $this->customerFactory->create()->load($mainCustomerId);
                $customerPricelistCode = $customerCollection->getData('pricelist_code');
                $customerId = $mainCustomerId;
            }
            $pricelistStatus = $this->getPricelistStatus(
                $customerPricelistCode,
                $websiteId
            );
            $subtotal = 0;
            if ($customerId) {
                foreach ($subject->getQuote()->getAllItems() as $r) {
                    $subtotal += $r->getRowTotal();
                }
                foreach ($subject->getQuote()->getAllItems() as $product) {
                    $productId = $product->getProductId();
                    $productTypeId = $product->getTypeId($productId);
                    if ($productTypeId != 'Bundle') {
                        $qtyItem = $product->getQty();
                        $productDetail = $this->productRepository->get($product->getSku());
                        $finalPrice = $productDetail->getFinalPrice($qtyItem);
                        if ($productTypeId == 'configurable') {
                            $productId = $this->getConfigProductIdBySku($product->getSku());
                            $configChildData = $this->loadConfigProduct($productId);
                            $finalPrice = $configChildData->getPrice();
                        }
                        $pricelistPrice = '';
                        if ($customerPricelistCode && $pricelistStatus) {
                            $pricelistPrice = $this->helperPricelist->getAmount(
                                $productId,
                                $finalPrice,
                                $customerPricelistCode,
                                true
                            );
                        }
                        $categoryIds = $productDetail->getCategoryIds();
                        if ($pricelistPrice) {
                            $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($pricelistPrice,
                                $customerId,
                                $categoryIds);
                        } else {
                            $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($finalPrice,
                                $customerId,
                                $categoryIds);
                        }

                        // for tier price
                        $tierPrice = '';
                        $productSku = $product->getSku();
                        $minimumOrderAmount = $this->helperTierprice->getMinimumOrderAmount($productSku, $customerId,
                            $websiteId, $qtyItem);
                        $tierPrice = $this->helperTierprice->getTierprice(
                            $productId,
                            $productSku,
                            $customerId,
                            $websiteId,
                            $qtyItem,
                            $finalPrice
                        );
                        if ($tierPrice) {
                            if (($subtotal - $product->getRowTotal() + $tierPrice * $product->getQty()) < $minimumOrderAmount) {
                                $tierPrice = '';
                            }
                        }

                        $specialPrice = '';

                        $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                            $productId,
                            $productSku,
                            $customerId,
                            $websiteId,
                            $finalPrice
                        );

                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            $finalPrice,
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice
                        );

                        $customQuotationPrice = $product->getCustomQuotationPrice();
                        if ($customQuotationPrice) {
                            $actualPrice = $customQuotationPrice;
                        }
                        $this->setCustomPrices($product, $actualPrice);
                    }

                }
            }

        }

        return $result;
    }

    /**
     * beforeAddProduct
     *
     * @param Cart $subject
     * @param $productInfo
     * @param null $requestInfo
     * @return array
     */
    public function beforeAddProduct(\Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null)
    {
        if ($this->quotationHelper->isFromQuote()) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Please remove quote from cart"));
        }
        return [$productInfo,$requestInfo];
    }

    /**
     * beforeUpdateItems
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $data
     */
    public function beforeUpdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        if ($this->quotationHelper->isFromQuote()) {
            throw new \Magento\Framework\Exception\LocalizedException(__("This item cannot be updated"));
        }

        return [$data];
    }

    /**
     * GetPricelistStatus
     *
     * @param mixed $customerPricelistCode CustomerPricelistCode
     * @param int $websiteId WebsiteId
     *
     * @return NULL|int
     */
    public function getPricelistStatus($customerPricelistCode, $websiteId)
    {
        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        return $pricelistStatus;
    }

    /**
     * Get ConfigProductId By Sku
     *
     * @param mixed $sku Sku
     *
     * @return int
     */
    public function getConfigProductIdBySku($sku)
    {
        $id = null;
        $id = $this->productFactory->create()->getIdBySku($sku);
        return $id;
    }

    /**
     * Load ConfigProduct
     *
     * @param INT $productId ProductId
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function loadConfigProduct($productId)
    {
        return $this->productFactory->create()->load($productId);
    }

    /**
     * Set CustomPrices
     *
     * @param \Magento\Catalog\Model\Product $product Product
     * @param float $actualPrice ActualPrice
     *
     * @return void
     */
    public function setCustomPrices($product, $actualPrice)
    {
        $actualPrice = $this->convertPrice($actualPrice);
        $product->setCustomPrice($actualPrice);
        $product->setOriginalCustomPrice($actualPrice);
        $product->getProduct()->setIsSuperMode(true);
    }

    /**
     * ConvertPrice
     *
     * @param float $amount Amount
     * @param null|int $store Store
     * @param null|string $currency Currency
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function convertPrice($amount = 0, $store = null, $currency = null)
    {
        if ($store == null) {
            $store = $this->storeManager->getStore()->getStoreId();
        }
        $rate = $this->priceCurrencyInterface->convert($amount, $store, $currency);
        return $rate;

    }
}