<?php

namespace Appseconnect\PriceDisplayCustomization\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;

class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    public $divisionHelper;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $session,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Template\Context $context,
        \Magento\Framework\Pricing\Render\RendererPool $rendererPool,
        PriceInterface $price = null,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency, $localeDate);
        $this->_objectManager = $objectManager;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->customerSession = $session;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPricelist = $helperPricelist;
        $this->helperTierprice = $helperTierprice;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperPriceRule = $helperPriceRule;
        $this->httpContext = $httpContext;
        $this->_storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->scopeConfig = $scopeConfig;
        $this->divisionHelper = $divisionHelper;
    }

    public function getValue()
    {
        $actualPrice = null;
        $productId = parent::getProduct()->getId();
        $item = $this->productFactory->create()->load($productId);

        $qtyItem = 1;

        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');
        if ($customerType == 3) {
            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                if (isset($currentCustomerId)) {

                    // For division specific discount
                    $divisionCustomerId = $currentCustomerId;
                    $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
                    $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                    $customerId = $getCustomerDetails['customerId'];

                } else {
                    $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
                    $customerPricelistCode = $customerCollection->getData('pricelist_code');
                    $customerId = $customerDetail['customer_id'];
                }

            } else {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                // For division specific discount
                $divisionCustomerId = $customerDetail['customer_id'];
                $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
                $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                $customerId = $getCustomerDetails['customerId'];
            }

        }

        if ($customerId && $customerType == 3) {
            if ($customerId && $customerType != 3) {
                $finalPrice = $this->productFactory->create()
                    ->load($productId)
                    ->getPrice();
                return $finalPrice;
            }
            $pricelistStatus = null;
            $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
                ->addFieldToFilter('id', $customerPricelistCode)
                ->addFieldToFilter('website_id', $websiteId)
                ->getData();
            if (isset($pricelistCollection[0])) {
                $pricelistStatus = $pricelistCollection[0]['is_active'];
            }

            if ($customerType == 3) {
                $finalPrice = $this->productFactory->create()
                    ->load($productId)
                    ->getPrice();
                $pricelistPrice = '';
                if ($customerPricelistCode && $pricelistStatus) {
                    $pricelistPrice = $this->helperPricelist->getAmount(
                        $productId,
                        $finalPrice,
                        $customerPricelistCode,
                        true
                    );
                }
                $categoryIds = $this->productFactory->create()
                    ->load($productId)
                    ->getCategoryIds();
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
                $productSku = $item->getSku();
                $tierPrice = $this->helperTierprice->getTierprice(
                    $productId,
                    $productSku,
                    $customerId,
                    $websiteId,
                    $qtyItem,
                    $finalPrice
                );
                $specialPrice = '';
                $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                    $productId,
                    $productSku,
                    $customerId,
                    $websiteId,
                    $finalPrice
                );

                //Customer Group Discount
                $groupPrice = '';

                if ($item->getTypeId() != 'bundle' || $item->getTypeId() != 'configurable') {
                    if ($pricelistPrice) {
                        $finalPrice = $pricelistPrice;
                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            $finalPrice,
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice,
                            $groupPrice,
                            0,
                            0
                        );
                    } elseif ($categoryDiscountedPrice != '' || $specialPrice != '') {
                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            '',
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice,
                            $groupPrice,
                            0,
                            0
                        );
                    } elseif ($tierPrice != '') {
                        $actualPrice = $finalPrice;
                    } else {
                        $actualPrice = $finalPrice;
                    }
                }
                $actualPrice = $actualPrice;
            } else {
                $actualPrice = $this->productFactory->create()
                    ->load($productId)
                    ->getPrice();
            }
            return $actualPrice;
        } else {

            return parent::getValue();
        }
    }
}
