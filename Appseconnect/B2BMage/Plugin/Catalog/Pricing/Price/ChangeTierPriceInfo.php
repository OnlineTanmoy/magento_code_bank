<?php

namespace Appseconnect\B2BMage\Plugin\Catalog\Pricing\Price;

use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

class ChangeTierPriceInfo
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

    public $httpContext;

    public $divisionHelper;

    public $helperPriceRule;

    public $_storeManager;

    public $productFactory;

    public $customerFactory;

    /**
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    public $helperTierprice;
    public $helperCategory;
    public $helperCustomerSpecialPrice;
    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    public function __construct(
        \Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory $tierPriceCollectionFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Tierprice\CollectionFactory $tierPriceProductMapCollectionFactory,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\App\Http\Context $httpContext,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->tierPriceCollectionFactory = $tierPriceCollectionFactory;
        $this->customerSession = $session;
        $this->tierPriceProductMapCollectionFactory = $tierPriceProductMapCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->httpContext = $httpContext;
        $this->divisionHelper = $divisionHelper;
        $this->helperPriceRule = $helperPriceRule;
        $this->_storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->helperPricelist = $helperPricelist;
        $this->customerRepository = $customerRepository;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
    }

    /**
     * Get price value
     *
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $subject
     * @param \Closure $proceed
     *
     * @return void
     */
    public function aroundGetValue(
        \Magento\Catalog\Pricing\Price\FinalPrice $subject,
        \Closure $proceed
    ) {

        $finalTierPrice = [];

        $product = $subject->getProduct();
        $sku = $product->getSku();
        $productId = $product->getId();

        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        if ($customerId && $customerType == 3) {

            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                if (isset($currentCustomerId)) {

                    // For division specific discount
                    $divisionCustomerId = $currentCustomerId;
                    $getCustomerDetails = $this->getCustomerDetails($divisionCustomerId);
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
                $getCustomerDetails = $this->getCustomerDetails($divisionCustomerId);
                $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                $customerId = $getCustomerDetails['customerId'];
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
            $discountType = 0;
            if (!empty($tierPriceCollection)) {
                $tierPriceId = $tierPriceCollection['id'];
                $discountType = $tierPriceCollection['discount_type'];
            }

            $pricelistCollection = $this->pricelistPriceCollectionFactory->create();
            $pricelistCollection
                ->addFieldToFilter('id', $customerPricelistCode)
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('website_id', $websiteId);
            $pricelistData = $pricelistCollection->getData();
            $pricelistData = isset($pricelistData[0]) ? $pricelistData[0] : null;

            $finalPrice = $this->productFactory->create()
                ->load($productId)
                ->getPrice();

            if (!empty($pricelistData)) {
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

                $result = [];
                foreach ($tierPriceProductMapCollection as $tierPriceData) {

                    $tierPrice = $tierPriceData['tier_price'];

                    if ($discountType) {
                        $tierPrice = $finalPrice * (100 - $tierPriceData['tier_price']) / 100;
                    }

                    //Custom tier prices array
                    $result[] = [
                        "website_id" => $websiteId,
                        "customer_group_id" => $customerGroupId,
                        "qty" => $tierPriceData['quantity'],
                        "value" => $tierPrice
                    ];
                }

                if (!empty($result)) {
                    foreach ($result as $tier) {
                        $each["website_id"] = $tier["website_id"];
                        $each["cust_group"] = $tier["customer_group_id"];
                        $each["price_qty"] = $tier["qty"];
                        $each["price"] = $tier["value"];

                        $finalTierPrice[] = $each;
                    }
                }

                //Set modified tier prices array in product data
                if (!empty($finalTierPrice)) {
                    $product->setTierPrice($finalTierPrice);
                }
                if ($this->getPricelistPrice($subject->getProduct())) {
                    $product->setPrice($this->getPricelistPrice($product));
                }
            }
        }

        return $proceed();
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

    public function getPricelistPrice($product)
    {
        $productId = $product->getId();

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
            if ($pricelistPrice) {
                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($pricelistPrice,
                    $customerId,
                    $categoryIds);
            } else {
                $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount($finalPrice, $customerId,
                    $categoryIds);
            }
            // for tier price
            $tierPrice = '';
            $productSku = $product->getSku();
            $tierPrice = $this->helperTierprice->getTierprice(
                $productId,
                $productSku,
                $customerId,
                $websiteId,
                1,
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
                $actualPrice = $this->productFactory->create()
                    ->load($productId)
                    ->getPrice();;
            }
            return $actualPrice;
        }
    }

}