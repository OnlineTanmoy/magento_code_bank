<?php

namespace Appseconnect\B2BMage\Plugin\Catalog\Pricing\Price;

use Magento\Framework\Pricing\SaleableInterface;
use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

class MinimalTierPriceCalculator
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
    }

    /**
     * Get raw value of "as low as" as a minimal among tier prices{@inheritdoc}
     *
     * @param \Magento\Catalog\Pricing\Price\MinimalTierPriceCalculator $subject
     * @param \Closure $proceed
     * @param SaleableInterface $saleableItem
     *
     * @return float|null
     */

    public function aroundGetValue(
        \Magento\Catalog\Pricing\Price\MinimalTierPriceCalculator $subject,
        \Closure $proceed,
        SaleableInterface $saleableItem
    ) {

        $sku = $saleableItem->getSku();
        $productId = $saleableItem->getId();

        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

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
                    return $tierPrices ? min($tierPrices) : null;
                }
            }
        }

        return $proceed($saleableItem);
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