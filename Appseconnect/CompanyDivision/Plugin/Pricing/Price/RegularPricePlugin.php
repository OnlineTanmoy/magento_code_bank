<?php

namespace Appseconnect\CompanyDivision\Plugin\Pricing\Price;


use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;

class RegularPricePlugin
{
    protected $httpContext;
    protected $_storeManager;

    /**
     * RegularPricePlugin constructor.
     * @param Registry $registry
     * @param Session $session
     * @param CollectionFactory $pricelistPriceCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Registry $registry,
        Session $session,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    )
    {
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->customerSession = $session;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPricelist = $helperPricelist;
        $this->helperTierprice = $helperTierprice;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperPriceRule = $helperPriceRule;
        $this->priceHelper = $priceHelper;
        $this->customerRepository = $customerRepository;
        $this->calculator = $calculator;
        $this->priceCurrency = $priceCurrency;
        $this->httpContext = $httpContext;
        $this->_storeManager = $storeManager;
        $this->divisionHelper = $divisionHelper;
    }

    /**
     * Get price value
     *
     * @return float
     */
    public function afterGetValue(\Appseconnect\B2BMage\Pricing\Price\RegularPrice $subject, $result)
    {
        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $product = $subject->getProduct();
       //return $result;
        if ($customerType == 2) {
            return $result;
        }

        if ($customerType != 3) {
            return $result;
        } else if ($this->divisionHelper->isParentContact($customerId)) {
            return $result;
        } else {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

            // For division specific discount
            $divisionCustomerId = $customerDetail['customer_id'];
            $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
            $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
            $customerId = $getCustomerDetails['customerId'];
        }

        $item = $product;
        $qtyItem = 1;
        $productId = $item->getEntityId();

        $websiteId = $this->_storeManager->getStore()->getWebsiteId();


        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        $qtyItem = ($qtyItem) ? $qtyItem : 1;

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

        $price = $actualPrice;
        $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
        $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;

        return $this->value;

    }
}
