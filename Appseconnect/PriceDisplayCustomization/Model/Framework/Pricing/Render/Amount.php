<?php

namespace Appseconnect\PriceDisplayCustomization\Model\Framework\Pricing\Render;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;

class Amount extends \Magento\Framework\Pricing\Render\Amount
{
    protected $httpContext;

    protected $_storeManager;

    /**
     * @var  \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    public $divisionHelper;

    /**
     * Amount constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Session $session
     * @param CollectionFactory $pricelistPriceCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Template\Context $context
     * @param AmountInterface $amount
     * @param PriceCurrencyInterface $priceCurrency
     * @param RendererPool $rendererPool
     * @param SaleableInterface|null $saleableItem
     * @param PriceInterface|null $price
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
     * @param array $data
     */
    public function __construct(
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
        AmountInterface $amount,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Render\RendererPool $rendererPool,
        SaleableInterface $saleableItem = null,
        PriceInterface $price = null,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        array $data = []

    )
    {
        parent::__construct($context, $amount, $priceCurrency, $rendererPool, $saleableItem, $price, $data);
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

    public function getDiscountConfiguration()
    {
        return $this->scopeConfig
            ->getValue('discount_percentage_visibility/general/enable_discount_percentage_visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCurrentCustomerType()
    {
        return $this->httpContext->getValue('customer_type');
    }

    public function getDiscountPrice()
    {
        $actualPrice = null;
        $productId = $this->getSaleableItem()->getId();
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
            $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
                $finalPrice,
                $customerId,
                $categoryIds
            );
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
                }
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

            }
            $actualPrice = $actualPrice + $this->getPrice('final_price')->getAmount()->getTotalAdjustmentAmount();
        } else {
            $actualPrice = $this->productFactory->create()
                ->load($productId)
                ->getPrice();
        }
        return $actualPrice;
    }

    public function getProductBasePrice()
    {
        $finalPrice = '';
        $productId = $this->getSaleableItem()->getId();
        $finalPrice = $this->productFactory->create()
            ->load($productId)
            ->getPrice();

        $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($finalPrice);
        $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;

        return $this->value;
    }

    public function getStoreCurrencySymbol()
    {
        $currency = $this->currencyFactory->create();
        $currencySymbol = $currency->getCurrencySymbol();
        return $currencySymbol;
    }

    public function getDiscountPercent()
    {
        $productBasePrice = $this->getProductBasePrice();
        $discountedPrice  = $this->getDiscountPrice();
        if ($discountedPrice < $productBasePrice) {
            $discountedAmount  = $productBasePrice - $discountedPrice;
            $discountedPercent = 100 * ($discountedAmount) / $productBasePrice;

            return $discountedPercent;
        }
        return null;
    }

    public function getPricelistPrice()
    {
        $productId = $this->getSaleableItem()->getId();

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
            return $pricelistPrice;
        }
    }
}