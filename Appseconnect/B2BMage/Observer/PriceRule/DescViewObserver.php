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

/**
 * Class DescViewObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DescViewObserver implements ObserverInterface
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
     * DescViewObserver constructor.
     *
     * @param Session                                                $session                         Session
     * @param CollectionFactory                                      $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                $customerFactory                 CustomerFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data        $helperContactPerson             HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data     $helperCategory                  HelperCategory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data            $helperPricelist                 HelperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data    $helperTierprice                 HelperTierprice
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice      HelperCustomerSpecialPrice
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data            $helperPriceRule                 HelperPriceRule
     * @param \Magento\Catalog\Model\ProductFactory                  $productFactory                  ProductFactory
     */
    public function __construct(
        Session $session,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
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
        $item = $observer->getEvent()->getData('product');
        $qtyItem = $observer->getEvent()->getData('qty');
        $productId = $item->getEntityId();
        
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $websiteId = $this->customerSession->getCustomer()->getWebsiteId();
        ;
        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');
        
        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $customerDetail['customer_id'];
        }
        if ($customerType == 2) {
            return $this;
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
        
        if ($customerId) {
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

            if ($item->getTypeId() != 'bundle' || $item->getTypeId() != 'configurable') {
                if ($pricelistPrice) {
                    $finalPrice = $pricelistPrice;
                }
                $actualPrice = $this->helperPriceRule->getActualPrice(
                    $finalPrice,
                    $tierPrice,
                    $categoryDiscountedPrice,
                    $pricelistPrice,
                    $specialPrice
                );
                $item->setPrice($actualPrice);
            }
            return $this;
        }
    }
}
