<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\PriceRule;

use Magento\Framework\Event\ObserverInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Product\Type;

/**
 * Class UpdateObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdateObserver implements ObserverInterface
{
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * ProductFactory
     *
     * @var Magento\Catalog\Model\ProductFactory
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
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;
    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;
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
     * UpdateObserver constructor.
     *
     * @param Session $session Session
     * @param CollectionFactory $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory HelperCategory
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice HelperTierprice
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist HelperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice HelperCustomerSpecialPrice
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule HelperPriceRule
     * @param \Magento\Catalog\Model\ProductFactory $productFactory ProductFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface PriceCurrencyInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface StoreManagerInterface
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository CustomerRepository
     */
    public function __construct(
        Session $session,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperTierprice = $helperTierprice;
        $this->helperPricelist = $helperPricelist;
        $this->helperPriceRule = $helperPriceRule;
        $this->divisionHelper = $divisionHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $websiteId = $this->customerSession->getCustomer()->getWebsiteId();
        $qtyItem = 1;

        if ($customerType == 2) {
            return $this;
        }

        $parentRuleApplied = 0;
        if ($customerType != 3) {
            return $this;
        } else {
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

        $pricelistStatus = $this->getPricelistStatus(
            $customerPricelistCode,
            $websiteId
        );
        $item = $observer->getEvent()->getCart();
        $subtotal = $item->getQuote()->getSubtotal();
        if ($customerId) {
            foreach ($item->getQuote()->getAllItems() as $product) {
                $productId = $product->getProductId();
                $productTypeId = $this->getTypeId($productId);
                if ($productTypeId != Type::TYPE_BUNDLE) {
                    $qtyItem = $product->getQty();
                    $productDetail = $this->loadProduct($productId);
                    $finalPrice = $productDetail->getPrice($qtyItem);
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

                    if ($pricelistPrice) {
                        $finalPrice = $pricelistPrice;
                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            $finalPrice,
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice
                        );
                    } elseif ($tierPrice != '' || $categoryDiscountedPrice != '' || $specialPrice != '') {
                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            '',
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice
                        );
                    } else {
                        $actualPrice = $finalPrice;
                    }
                    $customQuotationPrice = $product->getCustomQuotationPrice();
                    if ($customQuotationPrice) {
                        $actualPrice = $customQuotationPrice;
                    }
                    $this->setCustomPrices($product, $actualPrice);
                }
            }
            return $this;
        }
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
     * Get TypeId
     *
     * @param int $id Id
     *
     * @return string
     */
    public function getTypeId($id)
    {
        $typeId = null;
        $typeId = $this->productFactory->create()
            ->load($id)
            ->getTypeId();
        return $typeId;
    }

    /**
     * LoadProduct
     *
     * @param int $productId ProductId
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function loadProduct($productId)
    {
        return $this->productFactory->create()->load($productId);
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
            $store = $this->storeManagerInterface->getStore()->getStoreId();
        }
        $rate = $this->priceCurrencyInterface->convert($amount, $store, $currency);
        return $rate;

    }
}
