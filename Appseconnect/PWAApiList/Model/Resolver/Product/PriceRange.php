<?php
declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Product;

use Magento\CatalogGraphQl\Model\Resolver\Product\Price\Discount;
use Magento\CatalogGraphQl\Model\Resolver\Product\Price\ProviderPool as PriceProviderPool;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Helper\Data as TaxHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\CatalogGraphQl\Model\Resolver\Product\PriceRange as CorePriceRange;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Catalog\Model\ProductCategoryList;

/**
 * Format product's pricing information for price_range field
 */
class PriceRange extends \ScandiPWA\CatalogGraphQl\Model\Resolver\Product\PriceRange
{
    public $contactHelper;

    public $customerFactory;

    public $specialPriceHelper;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $priceListHelper;

    public $newContext;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;

    public $categoryDiscountHelper;

    public $productCategory;

    /**
     * @param PriceProviderPool $priceProviderPool
     * @param Discount $discount
     */
    public function __construct(
        PriceProviderPool                                      $priceProviderPool,
        Discount                                               $discount,
        PriceCurrencyInterface                                 $priceCurrency,
        ScopeConfigInterface                                   $scopeConfig,
        TaxHelper                                              $taxHelper,
        ProductCategoryList                                    $productCategory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data        $contactHelper,
        \Magento\Customer\Model\CustomerFactory                $customerFactory,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $specialPriceHelper,
        CollectionFactory                                      $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data            $priceListHelper,
        \Appseconnect\B2BMage\Helper\PriceRule\Data            $helperPriceRule,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data     $categoryDiscountHelper
    )
    {
        $this->contactHelper = $contactHelper;
        $this->customerFactory = $customerFactory;
        $this->specialPriceHelper = $specialPriceHelper;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->priceListHelper = $priceListHelper;
        $this->helperPriceRule = $helperPriceRule;
        $this->categoryDiscountHelper = $categoryDiscountHelper;
        $this->productCategory = $productCategory;
        parent::__construct(
            $priceProviderPool,
            $discount,
            $priceCurrency,
            $scopeConfig,
            $taxHelper
        );
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $this->newContext = $context;

        /** @var StoreInterface $store */
        $store = $context->getExtensionAttributes()->getStore();

        /** @var Product $product */
        $product = $value['model'];

        $requestedFields = $info->getFieldSelection(10);
        $returnArray = [];

        if (isset($requestedFields['minimum_price'])) {
            $returnArray['minimum_price'] = $this->getMinimumProductPrice($product, $store);
        }
        if (isset($requestedFields['maximum_price'])) {
            $returnArray['maximum_price'] = $this->getMaximumProductPrice($product, $store);
        }
        return $returnArray;
    }

    /**
     * Get formatted minimum product price
     *
     * @param SaleableInterface $product
     * @param StoreInterface $store
     * @return array
     */
    public function getMinimumProductPrice(SaleableInterface $product, StoreInterface $store): array
    {
        $productId = $product->getId();
        $productSku = $product->getSku();
        $productPrice = $product->getPrice();
        $categoryids = $this->productCategory->getCategoryIds($product->getId());
        $currentCustomerId = $this->newContext->getUserId();
        $customer = $this->customerFactory->create()->load($currentCustomerId);

        $websiteId = $customer->getWebsiteId();
        $customerSpecialPrice = '';
        $pricelistStatus = null;
        $pricelistPrice = '';
        $tierPrice = '';
        $categoryDiscountedPrice = '';
        $actualPrice = '';

        if ($currentCustomerId) {
            if ($this->contactHelper->isContactPerson($customer)) {
                $customerId = $this->contactHelper->getContactCustomerId($currentCustomerId);

                // For Pricelist
                $customerCollection = $this->customerFactory->create()->load($customerId);
                $customerPricelistCode = $customerCollection->getData('pricelist_code');
                $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
                    ->addFieldToFilter('id', $customerPricelistCode)
                    ->addFieldToFilter('website_id', $websiteId)
                    ->getData();
                if (isset($pricelistCollection[0])) {
                    $pricelistStatus = $pricelistCollection[0]['is_active'];
                }
                if ($customerPricelistCode && $pricelistStatus) {
                    $pricelistPrice = $this->priceListHelper->getAmount(
                        $productId,
                        $productPrice,
                        $customerPricelistCode,
                        true
                    );
                }

                // For Special Price
                $customerSpecialPrice = $this->specialPriceHelper
                    ->getSpecialPrice($productId, $productSku, $customerId, $websiteId, $productPrice);

                // For Category Discount
                $categoryDiscountedPrice = $this->categoryDiscountHelper
                    ->getCategoryDiscountAmount($productPrice, $customerId, $categoryids);

                $actualPrice = $this->helperPriceRule->getActualPrice(
                    $productPrice,
                    $tierPrice,
                    $categoryDiscountedPrice,
                    $pricelistPrice,
                    $customerSpecialPrice
                );
            }
        }
        $priceProvider = $this->priceProviderPool->getProviderByProductType($product->getTypeId());

        $regularPrice = (float)$priceProvider->getMinimalRegularPrice($product)->getValue();
        $finalPrice = (float)$priceProvider->getMinimalFinalPrice($product)->getValue();

        $discount = $this->calculateDiscount($product, $regularPrice, $finalPrice);

        $regularPriceExclTax = (float)$priceProvider->getMinimalRegularPrice($product)->getBaseAmount();
        $finalPriceExclTax = (float)$priceProvider->getMinimalFinalPrice($product)->getBaseAmount();

        if ($product->getTypeId() == ProductType::TYPE_SIMPLE) {
            $priceInfo = $product->getPriceInfo();
            $defaultRegularPrice = $priceInfo->getPrice(RegularPrice::PRICE_CODE)->getAmount()->getValue();
            $defaultFinalPrice = $priceInfo->getPrice(FinalPrice::PRICE_CODE)->getAmount()->getValue();
            $defaultFinalPriceExclTax = $priceInfo->getPrice(FinalPrice::PRICE_CODE)->getAmount()->getBaseAmount();

            $discount = $this->calculateDiscount($product, $defaultRegularPrice, $defaultFinalPrice);
        } else {
            $defaultRegularPrice = $this->taxHelper->getTaxPrice($product, $product->getPrice(),
                $this->isPriceIncludesTax());
            $defaultFinalPrice = (float)round($priceProvider->getRegularPrice($product)->getValue(), 2);
            $defaultFinalPriceExclTax = (float)$priceProvider->getRegularPrice($product)->getBaseAmount();
        }

        $defaultRegularPrice = isset($defaultRegularPrice) ? $defaultRegularPrice : 0;
        $defaultFinalPrice = isset($defaultFinalPrice) ? $defaultFinalPrice : 0;
        $defaultFinalPriceExclTax = isset($defaultFinalPriceExclTax) ? $defaultFinalPriceExclTax : 0;

        if ($this->contactHelper->isContactPerson($customer)) {
            if ($actualPrice && $actualPrice != $productPrice) {
                $finalPriceExclTax = (float)$actualPrice;
                $finalPrice = (float)$actualPrice;

                if ($pricelistPrice) {
                    $regularPrice = (float)$pricelistPrice;
                    $regularPriceExclTax = (float)$pricelistPrice;
                }
            } elseif ($actualPrice == 0) {
                $finalPriceExclTax = 0;
                $finalPrice = 0;

                if ($pricelistPrice) {
                    $regularPrice = (float)$pricelistPrice;
                    $regularPriceExclTax = (float)$pricelistPrice;
                }
            } else {
                $finalPriceExclTax = (float)$finalPrice;
            }
        }
        $discount = $this->calculateDiscount($product, $regularPrice, $finalPrice);

        if ($finalPriceExclTax == $pricelistPrice) {
            $finalPrice = $regularPrice;
            $discount = $this->calculateDiscount($product, $regularPrice, $finalPrice);
        }

        if ($this->contactHelper->isContactPerson($customer)) {
            if ($actualPrice == 0) {
                $finalPrice = 0;
                $discount = $this->calculateDiscount($product, $regularPrice, $finalPrice);
            }
        }


        $minPriceArray = $this->formatPrice(
            $regularPrice, $regularPriceExclTax, $finalPrice, $finalPriceExclTax,
            $defaultRegularPrice, $defaultFinalPrice, $defaultFinalPriceExclTax, $discount, $store
        );
        $minPriceArray['model'] = $product;
        return $minPriceArray;
    }
}
