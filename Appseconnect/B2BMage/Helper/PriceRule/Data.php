<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Helper\PriceRule;

use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\Price\Collection as PricelistPriceCollection;
use Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data as SpecialPriceHelper;
use Insync\B2BMage\Block\ContactPerson\Address\Book;
use Magento\Catalog\Model\Product\Type;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * PricelistPriceCollection
     *
     * @var PricelistPriceCollection
     */
    public $pricelistCollection;

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * ProductFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryDiscount\Data
     */
    public $helperCategory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;

    /**
     * SpecialPriceHelper
     *
     * @var SpecialPriceHelper
     */
    public $helperCustomerSpecialPrice;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;
    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;
    public $divisionHelper;
    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;
    /**
     * PriceCurrencyInterface
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrencyInterface;

    /**
     * Data constructor.
     *
     * @param Context $context Context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param PricelistPriceCollection $pricelistCollection PricelistCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig ScopeConfig
     * @param Session $session Session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory HelperCategory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist HelperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice HelperTierprice
     * @param SpecialPriceHelper $helperCustomerSpecialPrice HelperCustomerSpecialPrice
     * @param \Magento\Catalog\Model\ProductFactory $productFactory ProductFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface PriceCurrencyInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface StoreManagerInterface
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository CustomerRepository
     * @param \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper DivisionHelper
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PricelistPriceCollection $pricelistCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        SpecialPriceHelper $helperCustomerSpecialPrice,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->pricelistCollection = $pricelistCollection;
        $this->scopeConfig = $scopeConfig;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperPricelist = $helperPricelist;
        $this->customerRepository = $customerRepository;
        $this->divisionHelper = $divisionHelper;
        parent::__construct($context);
    }

    /**
     * ProcessBundleProduct
     *
     * @param \Magento\Catalog\Model\Product $item Item
     * @param string $customerPricelistCode CustomerPricelistCode
     * @param boolean $pricelistStatus PricelistStatus
     * @param int $customerId CustomerId
     * @param int $websiteId WebsiteId
     *
     * @return void
     */
    public function processBundleProduct($item, $customerPricelistCode, $pricelistStatus, $customerId, $websiteId)
    {

        $product = $this
            ->productFactory
            ->create()
            ->load($item->getProductId());
        $qtyItem = $item->getQty();
        $qtyItem = ($qtyItem) ? $qtyItem : 1;
        $productId = $product->getId();
        $categoryIds = $product->getCategoryIds();

        if ($item->getProduct()->getPriceType() == 1) {
            $productFinalPrice = $item->getProduct()->getFinalPrice();
            $sku = $this->productFactory->create()->load($item->getProduct()->getId())->getSku();
            $pricelistAmount = '';
            if ($customerPricelistCode && $pricelistStatus) {
                $pricelistAmount = $this->helperPricelist->getAmount(
                    $item->getProduct()->getId(),
                    $item->getProduct()->getFinalPrice(),
                    $customerPricelistCode,
                    true
                );
            }
            $categoryDiscountedAmount = '';
            $categoryDiscountedAmount = $this->helperCategory->getCategoryDiscountAmount(
                $item->getProduct()->getFinalPrice(),
                $customerId,
                $categoryIds
            );
            $tierPriceAmount = '';
            $tierPriceAmount = $this->helperTierprice->getTierprice(
                $item->getProduct()->getId(),
                $sku,
                $customerId,
                $websiteId,
                $qtyItem,
                $item->getProduct()->getFinalPrice()
            );
            $specialPriceAmount = '';
            $specialPriceAmount = $this->helperCustomerSpecialPrice->getSpecialPrice(
                $item->getProduct()->getId(),
                $sku,
                $customerId,
                $websiteId,
                $item->getProduct()->getFinalPrice()
            );

            if ($pricelistAmount) {
                $productFinalPrice = $pricelistAmount;
            }
            $bundleCalculatedPriceAmount = $this->getActualPrice(
                $productFinalPrice,
                $tierPriceAmount,
                $categoryDiscountedAmount,
                $pricelistAmount,
                $specialPriceAmount
            );
            $bundleCalculatedPriceAmount = $this->convertPrice($bundleCalculatedPriceAmount);
            $item->setCustomPrice($bundleCalculatedPriceAmount);
            $item->setOriginalCustomPrice($bundleCalculatedPriceAmount);
        } else {
            foreach ($item->getQuote()->getAllItems() as $bundleItems) {
                if ($bundleItems->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                    continue;
                }
                $productPrice = $this->loadProduct($bundleItems->getProduct()->getEntityId())->getFinalPrice();
                $pricelistPrice = '';
                if ($customerPricelistCode && $pricelistStatus) {
                    $pricelistPrice = $this->helperPricelist->getAmount(
                        $bundleItems->getProduct()->getEntityId(),
                        $productPrice,
                        $customerPricelistCode,
                        true
                    );
                }
                $categoryDiscountedPrice = '';
                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
                    $productPrice,
                    $customerId,
                    $categoryIds
                );
                $productSku = $bundleItems->getProduct()
                    ->getSku();
                $tierPrice = '';
                $tierPrice = $this->helperTierprice->getTierprice(
                    $productId,
                    $productSku,
                    $customerId,
                    $websiteId,
                    $qtyItem,
                    $productPrice
                );

                $specialPrice = '';
                $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                    $bundleItems->getProduct()->getEntityId(),
                    $productSku,
                    $customerId,
                    $websiteId,
                    $productPrice
                );

                if ($pricelistPrice) {
                    $productPrice = $pricelistPrice;
                }
                $bundleCalculatedPrice = $this->getActualPrice(
                    $productPrice,
                    $tierPrice,
                    $categoryDiscountedPrice,
                    $pricelistPrice,
                    $specialPrice
                );

                $bundleCalculatedPrice = $this->convertPrice($bundleCalculatedPrice);
                $bundleItems->setCustomPrice($bundleCalculatedPrice);
                $bundleItems->setOriginalCustomPrice($bundleCalculatedPrice);
                $bundleItems->getProduct()
                    ->setIsSuperMode(true);
            }
        }

        $item->getProduct()
            ->setIsSuperMode(true);
    }

    /**
     * GetActualPrice
     *
     * @param float $finalPrice FinalPrice
     * @param float $tierPrice TierPrice
     * @param float $categoryDiscountedPrice CategoryDiscountedPrice
     * @param float $pricelistPrice PricelistPrice
     * @param float $specialPrice SpecialPrice
     *
     * @return float
     */
    public function getActualPrice($finalPrice, $tierPrice, $categoryDiscountedPrice, $pricelistPrice, $specialPrice)
    {
        $priorityStatus = $this
            ->scopeConfig
            ->getValue('insync_pricerule/setpriority/enable', 'store');
        $priority1 = $this
            ->scopeConfig
            ->getValue('insync_pricerule/setpriority/priority1', 'store');
        $priority2 = $this
            ->scopeConfig
            ->getValue('insync_pricerule/setpriority/priority2', 'store');
        $priority3 = $this
            ->scopeConfig
            ->getValue('insync_pricerule/setpriority/priority3', 'store');
        $priority4 = $this
            ->scopeConfig
            ->getValue('insync_pricerule/setpriority/priority4', 'store');

        if ($priorityStatus == 1) {
            $priorityPrice = '';

            $productPrice = [
                'final_price' => $finalPrice,
                'tier_price' => $tierPrice,
                'special_price' => $specialPrice,
                'category_price' => $categoryDiscountedPrice,
                'price_list' => $pricelistPrice
            ];

            if ($priority1 && $productPrice[$priority1]) {
                $priorityPrice = $productPrice[$priority1];
            } elseif ($priority2 && $productPrice[$priority2]) {
                $priorityPrice = $productPrice[$priority2];
            } elseif ($priority3 && $productPrice[$priority3]) {
                $priorityPrice = $productPrice[$priority3];
            } elseif ($priority4 && $productPrice[$priority4]) {
                $priorityPrice = $productPrice[$priority4];
            } else {
                $priorityPrice = $finalPrice;
            }
            return $priorityPrice;
        } else {
            $price = [$finalPrice, $tierPrice, $categoryDiscountedPrice, $pricelistPrice, $specialPrice];
            $minVal = max($finalPrice, $tierPrice, $categoryDiscountedPrice, $pricelistPrice, $specialPrice);
            foreach ($price as $value) {
                if (is_numeric($value) && $value < $minVal) {
                    $minVal = $value;
                }
            }
            $newPrice = $minVal;
            if ($newPrice <= 0) {
                return 0;
            } else {
                return $newPrice;
            }
        }
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
            $store = $this
                ->storeManagerInterface
                ->getStore()
                ->getStoreId();
        }
        $rate = $this
            ->priceCurrencyInterface
            ->convert($amount, $store, $currency);
        return $rate;
    }

    /**
     * LoadProduct
     *
     * @param int $id Id
     *
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function loadProduct($id)
    {
        $productModel = $this
            ->productFactory
            ->create();
        $productModel->load($id);
        return $productModel;
    }

    /**
     * ProcessConfigurableProduct
     *
     * @param \Magento\Catalog\Model\Product $item Item
     * @param string $customerPricelistCode CustomerPricelistCode
     * @param boolean $pricelistStatus PricelistStatus
     * @param int $customerId CustomerId
     * @param int $websiteId WebsiteId
     *
     * @return void
     */
    public function processConfigurableProduct($item, $customerPricelistCode, $pricelistStatus, $customerId, $websiteId)
    {
        $product = $this->productFactory->create()->load($item->getProductId());
        $quotationProductPrice = $this->customerSession->getQuotationProduct();
        $qtyItem = $item->getQty();
        $qtyItem = ($qtyItem) ? $qtyItem : 1;
        $categoryIds = $product->getCategoryIds();
        $quotationPrice = null;
        foreach ($item->getQuote()->getAllItems() as $configItems) {
            $productId = $configItems->getProduct()->getId();
            $customQuotationPrice = $configItems->getCustomQuotationPrice();
            if ($configItems->getId() && !$customQuotationPrice) {
                $quotationPrice = null;
            } else {
                if ($quotationProductPrice || $customQuotationPrice) {
                    if (isset($quotationProductPrice[$productId]) || $customQuotationPrice) {
                        $quotationPrice = ($customQuotationPrice) ? $customQuotationPrice : $quotationProductPrice[$productId];
                        $configItems->setCustomQuotationPrice($quotationPrice);
                        $configCalculatedPrice = $quotationPrice;
                    }
                }
            }
            if ($configItems->getProduct()->getTypeId() == 'configurable' && !$quotationPrice) {
                continue;
            }
            if ($quotationPrice == null) {
                $price = $configItems->getProduct()->getFinalPrice();

                $pricelistPrice = '';
                if ($customerPricelistCode && $pricelistStatus) {
                    $pricelistPrice = $this->helperPricelist->getAmount(
                        $configItems->getProduct()->getEntityId(),
                        $price,
                        $customerPricelistCode,
                        true
                    );
                }

                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($price, $customerId,
                    $categoryIds);

                $productSku = $configItems->getProduct()->getSku();
                $tierPrice = $this->helperTierprice->getTierprice(
                    $configItems->getProduct()->getEntityId(),
                    $productSku,
                    $customerId,
                    $websiteId,
                    $qtyItem,
                    $configItems->getProduct()->getFinalPrice()
                );

                $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                    $configItems->getProduct()->getEntityId(),
                    $productSku,
                    $customerId,
                    $websiteId,
                    $price
                );

                if ($pricelistPrice) {
                    $price = $pricelistPrice;
                }

                $configCalculatedPrice = $this->getActualPrice($price, $tierPrice, $categoryDiscountedPrice,
                    $pricelistPrice, $specialPrice);
            }
            $configCalculatedPrice = $this->convertPrice($configCalculatedPrice);
            $configItems->setCustomPrice($configCalculatedPrice);
            $configItems->setOriginalCustomPrice($configCalculatedPrice);
            $configItems->getProduct()
                ->setIsSuperMode(true);
        }
    }

    /**
     * ProcessSimpleProduct
     *
     * @param \Magento\Catalog\Model\Product $item Item
     * @param string $customerPricelistCode CustomerPricelistCode
     * @param boolean $pricelistStatus PricelistStatus
     * @param int $customerId CustomerId
     * @param int $websiteId WebsiteId
     *
     * @return void
     */
    public function processSimpleProduct($item, $customerPricelistCode, $pricelistStatus, $customerId, $websiteId)
    {
        $product = $this->productFactory->create()->load($item->getProductId());
        $quotationProductPrice = $this->customerSession->getQuotationProduct();
        $productId = $product->getId();
        $quotationPrice = null;
        $customQuotationPrice = $item->getCustomQuotationPrice();
        if ($quotationProductPrice || $customQuotationPrice) {
            if (isset($quotationProductPrice[$productId]) || $customQuotationPrice) {
                $quotationPrice = ($customQuotationPrice) ? $customQuotationPrice : $quotationProductPrice[$productId];
                $item->setCustomQuotationPrice($quotationPrice);
                $simpleCalculatedPrice = $quotationPrice;
            }
        }
        if ($quotationPrice == null) {
            $subtotal = $item->getQuote()->getSubtotal();
            foreach (array_reverse($item->getQuote()->getAllItems()) as $cartItems) {
                $product = $this->productFactory->create()->load($cartItems->getProductId());
                $finalPrice = $product->getPrice($cartItems->getQty());
                if ($product->getTypeId() == 'configurable') {
                    $finalPrice = $cartItems->getProduct()->getFinalPrice();
                }
                $productId = $product->getId();
                $categoryIds = $product->getCategoryIds();
                $qtyItem = $cartItems->getQty();
                $pricelistPrice = '';
                if ($customerPricelistCode && $pricelistStatus) {
                    $pricelistPrice = $this->helperPricelist->getAmount(
                        $productId,
                        $finalPrice,
                        $customerPricelistCode,
                        true
                    );
                }

                if ($pricelistPrice) {
                    $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($pricelistPrice,
                        $customerId,
                        $categoryIds);
                } else {
                    $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($finalPrice,
                        $customerId,
                        $categoryIds);
                }
                $productSku = $cartItems->getSku();
                $tierPrice = '';
                $minimumOrderAmount = $this->helperTierprice->getMinimumOrderAmount($productSku, $customerId,
                    $websiteId,
                    $qtyItem);

                $tierPrice = $this->helperTierprice->getTierprice($productId, $productSku, $customerId, $websiteId,
                    $qtyItem, $finalPrice);
                if ($tierPrice) {
                    if (!$cartItems->getRowTotal()) {
                        if ($subtotal + $tierPrice * $cartItems->getQty() < $minimumOrderAmount) {
                            $tierPrice = '';
                        }
                    } elseif (($subtotal - $cartItems->getRowTotal() + $tierPrice * $cartItems->getQty()) < $minimumOrderAmount) {
                        $tierPrice = '';
                    }
                }
                $specialPrice = '';
                $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice($productId, $productSku, $customerId,
                    $websiteId, $finalPrice);

                if ($pricelistPrice) {
                    $finalPrice = $pricelistPrice;
                    $simpleCalculatedPrice = $this->getActualPrice($finalPrice, $tierPrice, $categoryDiscountedPrice,
                        $pricelistPrice, $specialPrice);
                } elseif ($tierPrice != '' || $categoryDiscountedPrice != '' || $specialPrice != '') {
                    $simpleCalculatedPrice = $this->getActualPrice('', $tierPrice, $categoryDiscountedPrice,
                        $pricelistPrice, $specialPrice);
                } else {
                    $simpleCalculatedPrice = $finalPrice;
                }

                if ($cartItems->getCustomQuotationPrice()) {
                    $simpleCalculatedPrice = $cartItems->getCustomQuotationPrice();
                }
                $simpleCalculatedPrice = $this->convertPrice($simpleCalculatedPrice);
                $cartItems->setCustomPrice($simpleCalculatedPrice);
                $cartItems->setOriginalCustomPrice($simpleCalculatedPrice);
                $cartItems->getProduct()
                    ->setIsSuperMode(true);
                if (!$cartItems->getRowTotal()) {
                    $subtotal += $simpleCalculatedPrice;
                }
            }
        } else {
            $simpleCalculatedPrice = $this->convertPrice($simpleCalculatedPrice);
            $item->setCustomPrice($simpleCalculatedPrice);
            $item->setOriginalCustomPrice($simpleCalculatedPrice);
            $item->getProduct()
                ->setIsSuperMode(true);
        }
    }

    /**
     * GetDiscountedPrice
     *
     * @param int $productId ProductId
     * @param int $qty Qty
     * @param int $customerId CustomerId
     * @param int $websiteId WebsiteId
     *
     * @return float
     */
    public function getDiscountedPrice($productId, $qty, $customerId, $websiteId = null)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $product = $this->productFactory->create()->load($productId);
        $websiteId = $websiteId ? $websiteId : $this->customerSession->getCustomer()->getWebsiteId();
        $customerType = $customer->getCustomerType();
        $customerPricelistCode = $customer->getData('pricelist_code');
        $finalPrice = $product->getPrice($qty);

        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $customerDetail['customer_id'];
        }

        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistCollection
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();

        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        $pricelistPrice = '';
        if ($customerPricelistCode && $pricelistStatus) {
            $pricelistPrice = $this->helperPricelist->getAmount($productId, $finalPrice, $customerPricelistCode, true);
        }
        $categoryIds = $product->getCategoryIds();
        $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($finalPrice, $customerId,
            $categoryIds);
        // for tierprice
        $productSku = $product->getSku();
        $tierPrice = $this->helperTierprice->getTierprice($productId, $productSku, $customerId, $websiteId, $qty,
            $finalPrice);

        $specialPrice = '';
        $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice($productId, $productSku, $customerId,
            $websiteId, $finalPrice);

        $discountedPrice = '';
        $discountedPrice = $this->getActualPrice($finalPrice, $tierPrice, $categoryDiscountedPrice, $pricelistPrice,
            $specialPrice);
        return $discountedPrice;
    }

    public function getCustomerDetails($divisionCustomerId)
    {
        $customer = $this->customerRepository->getById($divisionCustomerId);
        if ($customer->getCustomAttribute('parent_rule_configuration') == null) {
            $parentRuleApplied = 0;
        } else {
            $parentRuleApplied = $customer->getCustomAttribute('parent_rule_configuration')->getValue();
        }

        $mainCustomerId = $this->divisionHelper->getMainCustomerId($divisionCustomerId);

        if ($parentRuleApplied) {
            $customerCollection = $this->customerFactory->create()->load($mainCustomerId);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $mainCustomerId;

        } else {
            $customerCollection = $this->customerFactory->create()->load($divisionCustomerId);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $divisionCustomerId;
        }

        return ['customerPricelistCode' => $customerPricelistCode, 'customerId' => $customerId];
    }
}

