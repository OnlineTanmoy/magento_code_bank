<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\CustomerTierPrice;

use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Appseconnect\B2BMage\Model\ResourceModel\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Appseconnect\B2BMage\Model\ProductFactory as CustomerTierProductFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory as TierProductCollectionFactory;
use Magento\Customer\Model\Session;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CustomerTierProductFactory
     */
    public $customerTierProductFactory;
    
    /**
     * @var ProductFactory
     */
    public $tierPriceResourceFactory;
    
    /**
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;
    
    /**
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;
    
    /**
     * @var TierProductCollectionFactory
     */
    public $tierProductCollectionFactory;
    
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

    public $httpContext;

    public $divisionHelper;

    public $customerFactory;

    public $customerSession;

    public $helperContactPerson;

    /**
     * Data constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist                 HelperPricelist
     * @param ProductFactory                              $tierPriceResourceFactory        TierPriceResourceFactory
     * @param CollectionFactory                           $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param ProductCollectionFactory                    $productCollectionFactory        ProductCollectionFactory
     * @param TierProductCollectionFactory                $tierProductCollectionFactory    TierProductCollectionFactory
     * @param CustomerTierProductFactory                  $customerTierProductFactory      CustomerTierProductFactory
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        ProductFactory $tierPriceResourceFactory,
        CollectionFactory $pricelistPriceCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        TierProductCollectionFactory $tierProductCollectionFactory,
        CustomerTierProductFactory $customerTierProductFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\App\Http\Context $httpContext,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->tierPriceResourceFactory = $tierPriceResourceFactory;
        $this->customerTierProductFactory = $customerTierProductFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->tierProductCollectionFactory = $tierProductCollectionFactory;
        $this->helperPricelist = $helperPricelist;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->httpContext = $httpContext;
        $this->divisionHelper = $divisionHelper;
    }

    /**
     * GetTierprice
     *
     * @param int    $productId  ProductId
     * @param string $productSku ProductSku
     * @param int    $customerId CustomerId
     * @param int    $websiteId  WebsiteId
     * @param int    $qtyItem    QtyItem
     * @param float  $finalPrice FinalPrice
     *
     * @return null|number
     */
    public function getTierprice(
        $productId,
        $productSku,
        $customerId,
        $websiteId,
        $qtyItem,
        $finalPrice
    ) {

        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');

        if ($customerType == 3) {
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
        }
    
        $tierprice = '';
        $tierpriceCollection = $this->tierProductCollectionFactory->create();
        $tierPriceResourceModel = $this->tierPriceResourceFactory->create();
        $tierpriceCollection = $tierPriceResourceModel->getAssignedProducts(
            $tierpriceCollection,
            $productSku,
            $qtyItem
        );
        
        $tierpriceCollection
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId);
        $tierpriceData = $tierpriceCollection->getData();
        $tierpriceData = isset($tierpriceData[0]) ? $tierpriceData[0] : null;
        
        if (! empty($tierpriceData)) {

            $tierpriceData['pricelist_id'] = $customerPricelistCode;

            $pricelistCollection = $this->pricelistPriceCollectionFactory->create();
            $pricelistCollection
                ->addFieldToFilter('id', $tierpriceData['pricelist_id'])
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('website_id', $websiteId);
            $pricelistData = $pricelistCollection->getData();
            $pricelistData = isset($pricelistData[0]) ? $pricelistData[0] : null;
            if (! empty($pricelistData)) {
                $finalPrice = $this->helperPricelist->getAmount(
                    $productId,
                    $finalPrice,
                    $tierpriceData['pricelist_id']
                );
            }
            
            $tierprice = $tierpriceData['tier_price'];
            $discountType = $tierpriceData['discount_type'];
            
            if ($discountType) {
                $tierprice = $finalPrice * (100 - $tierprice) / 100;
            }
        }
        return $tierprice;
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

    /**
     * GetActualPrice
     *
     * @param float $finalPrice              FinalPrice
     * @param float $tierprice               Tierprice
     * @param float $categoryDiscountedPrice CategoryDiscountedPrice
     * @param float $pricelistPrice          PricelistPrice
     *
     * @return mixed
     */
    public function getActualPrice(
        $finalPrice,
        $tierprice,
        $categoryDiscountedPrice,
        $pricelistPrice
    ) {
    
        $price = [
            $finalPrice,
            $tierprice,
            $categoryDiscountedPrice,
            $pricelistPrice
        ];
        return min(array_filter($price));
    }

    /**
     * GetAssignedCustomerId
     *
     * @param int $tierPriceId TierPriceId
     *
     * @return array
     */
    public function getAssignedCustomerId($tierPriceId = null)
    {
        $customerCollection = $this->customerTierProductFactory->create()
            ->getCollection();
        
        $output = $customerCollection->getData();
        $result = [];
        
        foreach ($output as $val) {
            if ($tierPriceId != $val['id']) {
                $result[] = $val['customer_id'];
            }
        }
        
        return $result;
    }

    /**
     * GetAllProduct
     *
     * @param string $productSku ProductSku
     *
     * @return array
     */
    public function getAllProduct($productSku = null)
    {
        $collection = $this->productCollectionFactory->create();
        if ($productSku) {
            $collection->addAttributeToFilter(
                'sku', [
                'like' => '%' . $productSku . '%'
                ]
            );
        }
        $collection->addAttributeToSelect('*')->load();
        $collection->setCurPage(20);
        
        $productSku = [];
        foreach ($collection as $product) {
            $productSku['sku'][] = $product->getSku();
            $productSku['name'][] = $product->getName();
        }
        return $productSku;
    }

    /**
     * getMinimumOrderAmount
     * @param $productSku
     * @param $customerId
     * @param $websiteId
     * @param $qtyItem
     * @return int|mixed
     */
    public function getMinimumOrderAmount(
        $productSku,
        $customerId,
        $websiteId,
        $qtyItem
    ){
        $tierpriceCollection = $this->tierProductCollectionFactory->create();
        $tierPriceResourceModel = $this->tierPriceResourceFactory->create();
        $tierpriceCollection = $tierPriceResourceModel->getAssignedProducts(
            $tierpriceCollection,
            $productSku,
            $qtyItem
        );

        $tierpriceCollection
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId);
        $tierpriceData = $tierpriceCollection->getData();
        $tierpriceData = isset($tierpriceData[0]) ? $tierpriceData[0] : null;
        if (! empty($tierpriceData)) {
            return $tierpriceData['minimum_order_amount'];
        }
        return 0;
    }
}
