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

use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GridViewObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class GridViewObserver implements ObserverInterface
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

    public $divisionHelper;

    /**
     * GridViewObserver constructor.
     *
     * @param Session                                                $session                         Session
     * @param CollectionFactory                                      $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                $customerFactory                 CustomerFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data        $helperContactPerson             HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data     $helperCategory                  HelperCategory
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data    $helperTierprice                 HelperTierprice
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data            $helperPricelist                 HelperPricelist
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
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->customerSession = $session;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPriceRule = $helperPriceRule;
        $this->helperTierprice = $helperTierprice;
        $this->helperPricelist = $helperPricelist;
        $this->helperContactPerson = $helperContactPerson;
        $this->divisionHelper = $divisionHelper;
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

        $item = $observer->getEvent()->getCollection();
        if ($customerId) {
            foreach ($item as $product) {
                $productId = $product->getEntityId();
                $finalPrice = $product->getPrice($qtyItem);

                if ($product->getTypeId() == 'simple') {
                    $pricelistPrice = '';
                    if ($customerPricelistCode && $pricelistStatus) {
                        $pricelistPrice = $this->helperPricelist->getAmount(
                            $productId,
                            $finalPrice,
                            $customerPricelistCode,
                            true
                        );
                    }
                    $categoryIds = $this->getCategoryIdsByProductId($productId);
                    $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
                        $finalPrice,
                        $customerId,
                        $categoryIds
                    );

                    $productSku = $product->getSku();
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

                    $actualPrice = '';

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

                    $product->setPrice($actualPrice);
                }
            }

            return $this;
        }
    }

    /**
     * Get CategoryIds By ProductId
     *
     * @param int $productId ProductId
     *
     * @return mixed
     */
    public function getCategoryIdsByProductId($productId)
    {
        $ids = null;
        $ids = $this->productFactory->create()
            ->load($productId)
            ->getCategoryIds();

        return $ids;
    }
}
