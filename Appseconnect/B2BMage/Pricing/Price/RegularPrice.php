<?php
namespace Appseconnect\B2BMage\Pricing\Price;

use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;

class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice
{
    protected $httpContext;
    protected $_storeManager;

    public $divisionHelper;

    /**
     * Construct
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
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
        SaleableInterface $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        array $data = []

    ) {
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
        $this->product = $saleableItem;
        $this->quantity = $quantity;
        $this->calculator = $calculator;
        $this->priceCurrency = $priceCurrency;
        $this->priceInfo = $saleableItem->getPriceInfo();
        $this->httpContext = $httpContext;
        $this->_storeManager = $storeManager;
        $this->divisionHelper = $divisionHelper;
        parent::__construct($saleableItem,$quantity, $calculator, $priceCurrency);
    }

    /**
     * Get price value
     *
     * @return float
     */
    public function getValue()
    {
        $item = $this->product;
        $qtyItem = 1;
        $productId = $item->getEntityId();

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
        if ($customerType == 2) {
            return parent::getValue();
        }
        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        $qtyItem = ($qtyItem) ? $qtyItem : 1;

        // Need to fix pricelist price display issue for division company
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

            if($pricelistPrice != $actualPrice) {
                if ($customerType == 3 && $pricelistPrice != '') {
                    return $pricelistPrice;
                } else {
                    return parent::getValue();
                }
            } else {
                $price = $actualPrice;
                $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
                $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;

                return $this->value;
            }

        } else {
            return parent::getValue();
        }
    }
}
