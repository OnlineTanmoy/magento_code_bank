<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\TierPrice;

use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

/**
 * Interface Listing
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Display extends \Magento\Framework\View\Element\Template
{
    public $tierPriceCollectionFactory;

    public $tierPriceProductMapCollectionFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    public $helperContactPerson;

    public $registry;

    public $httpContext;

    public $divisionHelper;

    public $helperPriceRule;

    public $_storeManager;

    public $priceCurrency;

    public $customerFactory;

    public $pricelistPriceCollectionFactory;

    public $productFactory;

    public $helperPricelist;

    public $helperCategory;

    public $helperTierprice;

    public $helperCustomerSpecialPrice;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory $tierPriceCollectionFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Tierprice\CollectionFactory $tierPriceProductMapCollectionFactory,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        array $data = []
    ) {
        $this->tierPriceCollectionFactory = $tierPriceCollectionFactory;
        $this->customerSession = $session;
        $this->tierPriceProductMapCollectionFactory = $tierPriceProductMapCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->registry = $registry;
        $this->httpContext = $httpContext;
        $this->divisionHelper = $divisionHelper;
        $this->helperPriceRule = $helperPriceRule;
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->productFactory = $productFactory;
        $this->helperPricelist = $helperPricelist;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        parent::__construct($context, $data);
    }

    public function displayTierPriceData()
    {
        $currentProduct = $this->registry->registry('current_product');
        $sku = $currentProduct->getSku();
        $productId = $currentProduct->getId();
        $qtyItem = 1;

        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');

        if ($customerId && $customerType == 3) {

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

            $pricelistStatus = null;
            $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
                ->addFieldToFilter('id', $customerPricelistCode)
                ->addFieldToFilter('website_id', $websiteId)
                ->addFieldToFilter('is_active', 1)
                ->getData();
            if (isset($pricelistCollection[0])) {
                $pricelistStatus = $pricelistCollection[0]['is_active'];
            }
            $qtyItem = ($qtyItem) ? $qtyItem : 1;

            $pricelistData = $pricelistCollection;
            $pricelistData = isset($pricelistData[0]) ? $pricelistData[0] : null;

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

            if($pricelistPrice){
                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($pricelistPrice, $customerId,
                    $categoryIds);
            }
            else{
                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($finalPrice, $customerId,
                    $categoryIds);
            }

            // for tier price
            $tierPrice = '';
            $productSku = $sku;
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
            $actualPrice = '';

            if ($currentProduct->getTypeId() != 'bundle' || $currentProduct->getTypeId() != 'configurable') {
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

            $tierPriceCollection = $this->tierPriceCollectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('website_id', $websiteId)
                ->addFieldToFilter('is_active', 1)
                ->addFieldToSelect('id')
                ->addFieldToSelect('minimum_order_amount')
                ->addFieldToSelect('discount_type')
                ->getFirstItem()
                ->getData();

            $tierPriceId = 0;
            $minimumOrderAmount = 0;
            $discountType = 0;
            if (!empty($tierPriceCollection)) {
                $tierPriceId = $tierPriceCollection['id'];
                $minimumOrderAmount = $tierPriceCollection['minimum_order_amount'];
                $discountType = $tierPriceCollection['discount_type'];
            }

            if (! empty($pricelistData)) {
                $finalPrice = $this->helperPricelist->getAmount(
                    $productId,
                    $finalPrice,
                    $customerPricelistCode
                );
            }

            if ($tierPriceId) {
                $tierPriceProductMapCollection = $this->tierPriceProductMapCollectionFactory->create()
                    ->addFieldToFilter('parent_id', $tierPriceId)
                    ->addFieldToFilter('product_sku', $sku)
                    ->getData();

                $tierPrices = [];
                foreach ($tierPriceProductMapCollection as $tierPriceData) {

                    $tierPrice = $tierPriceData['tier_price'];

                    if ($discountType) {
                        $tierPrice = $finalPrice * (100 - $tierPriceData['tier_price']) / 100;
                    }

                    $tierPrices[] = $tierPrice;
                }

                if (!empty($tierPrices)) {
                    $tierPrices = min($tierPrices);
                }

                return ['minimumOrderAmount' => $minimumOrderAmount, 'tierPriceProductMapCollection' => $tierPrices,
                    'actualPrice' => $actualPrice];
            }
        }
    }

    public function convertCurrency($amount)
    {
        $convertedAmount = $this->priceCurrency->convertAndFormat($amount);

        return $convertedAmount;
    }
}