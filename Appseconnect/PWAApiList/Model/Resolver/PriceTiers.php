<?php

namespace Appseconnect\PWAApiList\Model\Resolver;

use Magento\CatalogCustomerGraphQl\Model\Resolver\PriceTiers as SourcePriceTiers;
use Magento\Catalog\Api\Data\ProductTierPriceInterface;
use Magento\CatalogCustomerGraphQl\Model\Resolver\Customer\GetCustomerGroup;
use Magento\CatalogCustomerGraphQl\Model\Resolver\Product\Price\Tiers;
use Magento\CatalogCustomerGraphQl\Model\Resolver\Product\Price\TiersFactory;
use Magento\CatalogGraphQl\Model\Resolver\Product\Price\Discount;
use Magento\CatalogGraphQl\Model\Resolver\Product\Price\ProviderPool as PriceProviderPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory as TierProductCollectionFactory;
use Magento\Catalog\Model\ProductCategoryList;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;


class PriceTiers extends \ScandiPWA\CatalogCustomerGraphQl\Model\Resolver\PriceTiers
{
    /**
     * @var ContactHelperdata
     */
    public $contactHelperdata;
    /**
     * @var CustomerFactory
     */
    public $customerFactory;
    /**
     * @var TierPricePersistence
     */
    public $tierPricePersistence;
    /**
     * @var Productloader
     */
    public $_productloader;
    /**
     * @var PriceListHelper
     */
    public $priceListHelper;
    /**
     * @var TiersFactory
     */
    protected $tiersFactory;
    /**
     * @var ValueFactory
     */
    protected $valueFactory;
    /**
     * @var GetCustomerGroup
     */
    protected $getCustomerGroup;
    /**
     * @var int
     */
    protected $customerGroupId;
    /**
     * @var Tiers
     */
    protected $tiers;
    /**
     * @var Discount
     */
    protected $discount;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var array
     */
    protected $tierPricesQty = [];

    /**
     * @param ValueFactory $valueFactory
     * @param TiersFactory $tiersFactory
     * @param GetCustomerGroup $getCustomerGroup
     * @param Discount $discount
     * @param PriceProviderPool $priceProviderPool
     * @param PriceCurrencyInterface $priceCurrency
     * @param ContactHelperdata $contactHelperdata
     * @param CustomerFactory $customerFactory
     * @param TierPricePersistence $tierPricePersistence
     * @param Productloader $_productloader
     * @param PriceListHelper $priceListHelper
     */
    public function __construct(
        ValueFactory $valueFactory,
        TiersFactory $tiersFactory,
        GetCustomerGroup $getCustomerGroup,
        Discount $discount,
        PriceProviderPool $priceProviderPool,
        PriceCurrencyInterface $priceCurrency,
        ProductCategoryList $productCategory,
        CollectionFactory $pricelistPriceCollectionFactory,
        TierProductCollectionFactory $tierProductCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelperdata,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\Product\Price\TierPricePersistence $tierPricePersistence,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $priceListHelper,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $specialPriceHelper,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $categoryDiscountHelper,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule
    ) {
        parent::__construct(
            $valueFactory,
            $tiersFactory,
            $getCustomerGroup,
            $discount,
            $priceProviderPool,
            $priceCurrency
        );
        $this->tiersFactory = $tiersFactory;
        $this->valueFactory = $valueFactory;
        $this->getCustomerGroup = $getCustomerGroup;
        $this->discount = $discount;
        $this->priceCurrency = $priceCurrency;
        $this->tierProductCollectionFactory = $tierProductCollectionFactory;
        $this->contactHelperdata = $contactHelperdata;
        $this->customerFactory = $customerFactory;
        $this->tierPricePersistence = $tierPricePersistence;
        $this->_productloader = $_productloader;
        $this->priceListHelper = $priceListHelper;
        $this->specialPriceHelper = $specialPriceHelper;
        $this->categoryDiscountHelper = $categoryDiscountHelper;
        $this->productCategory = $productCategory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->helperPriceRule = $helperPriceRule;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        if (empty($this->tiers)) {
            $this->customerGroupId = $this->getCustomerGroup->execute($context->getUserId());
            $this->tiers = $this->tiersFactory->create(['customerGroupId' => $this->customerGroupId]);
        }
        $product = $value['model'];
        $productId = (int)$product->getId();
        $currentCustomerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($currentCustomerId);
        $websiteId = $customer->getWebsiteId();
        $categoryids = $this->productCategory->getCategoryIds($product->getId());
        $customerSpecialPrice = '';
        $categoryDiscountedPrice = '';
        $pricelistPrice = '';
        $pricelistStatus = null;
        $tierData = array();
        if ($currentCustomerId) {
            if ($this->contactHelperdata->isContactPerson($customer)) {
                $customerId = $this->contactHelperdata->getContactCustomerId($currentCustomerId);
                $product = $this->_productloader->create()->load($productId);
                $tierpriceCollection = $this->tierProductCollectionFactory->create();
                $tierpriceCollection
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('is_active', 1);
                $tierpriceCollection->getSelect()
                    ->where("map.product_sku = ?", $product->getSku())
                    ->order('map.quantity  DESC')
                    ->join(
                        ['map' => 'insync_tierprice_map'],
                        'main_table.id = map.parent_id',
                        [
                            'parent_id' => 'parent_id',
                            'quantity' => 'quantity',
                            'tier_price' => 'tier_price'
                        ]
                    );
                $tierData = $tierpriceCollection->getData();
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
                        $product->getPrice(),
                        $customerPricelistCode,
                        true
                    );
                }
                // For Special Price
                $customerSpecialPrice = $this->specialPriceHelper
                    ->getSpecialPrice($productId, $product->getSku(), $customerId, $websiteId, $product->getPrice());

                // For Category Discount
                $categoryDiscountedPrice = $this->categoryDiscountHelper
                    ->getCategoryDiscountAmount($product->getPrice(), $customerId, $categoryids);

                $actualPrice = $this->helperPriceRule->getActualPrice(
                    $product->getPrice(),
                    null,
                    $categoryDiscountedPrice,
                    $pricelistPrice,
                    $customerSpecialPrice
                );
            }
        }
        $tierPrices = array();
        $this->tiers->addProductFilter($productId);
        if (!empty($tierData) && $actualPrice>0) {
            $currencyCode = $context->getExtensionAttributes()->getStore()->getCurrentCurrencyCode();
            $productPrice = $this->tiers->getProductRegularPrice($productId) ?? 0.0;
            foreach ($tierData as $tierPrice) {
                if ($tierPrice['pricelist_id'] != 0) {
                    $pricelistPrice = $this->priceListHelper->getAmount(
                        $productId,
                        $productPrice,
                        $tierPrice['pricelist_id'],
                        true
                    );
                    $productPrice = $pricelistPrice;
                }
                if (!$tierPrice['discount_type']) {
                    $discount = array(
                        'amount_off' => ($productPrice - $tierPrice['tier_price']),
                        'percent_off' => round((($productPrice - $tierPrice['tier_price']) / $productPrice) * 100)
                    );
                    $tierPriceAll = $tierPrice['tier_price'];
                } else {
                    $discount = array(
                        'amount_off' => ($tierPrice['tier_price'] / 100) * $productPrice,
                        'percent_off' => $tierPrice['tier_price']
                    );
                    $tierPriceAll = $productPrice - (($tierPrice['tier_price'] / 100) * $productPrice);
                    if($tierPriceAll<0){
                        $tierPriceAll=0;
                    }
                }
                $tierPrices[] = array(
                    'discount' => $discount,
                    'final_price' => array('currency' => $currencyCode, 'value' => $tierPriceAll),
                    'quantity' => $tierPrice['quantity']
                );
            }
            return $tierPrices;
        } elseif($customerSpecialPrice=='' && $categoryDiscountedPrice=='' && $pricelistPrice=='') {
            return parent::resolve($field, $context, $info, $value, $args);
        }
    }
}
