<?php

namespace Appseconnect\MultipleDiscounts\Helper;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemFactory;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Customer\Model\Session;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory
     */
    public $discountMapFactory;

    /**
     * @var CollectionFactory
     */
    public $websiteCollectionFactory;

    public $cart;

    /**
     * @var ItemFactory
     */
    public $itemFactory;

    public $quoteRepository;

    public $serializer;

    public $mathRandom;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory
     */
    public $discountCollectionFactory;

    /**
     * @var QuoteItemFactory
     */
    public $quoteItemFactory;

    /**
     * @var ProductCategoryList
     */
    public $productCategory;

    /**
     * @var Session
     */
    public $customerSession;

    public $storeManager;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    public $helperCompanyDivision;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory
     */
    public $discountMapCollectionFactory;

    public $productRepository;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @param \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory $discountMapFactory
     * @param CollectionFactory $websiteCollectionFactory
     * @param Cart $cart
     * @param ItemFactory $itemFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory $discountCollectionFactory
     * @param QuoteItemFactory $quoteItemFactory
     * @param ProductCategoryList $productCategory
     * @param Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Appseconnect\CompanyDivision\Helper\Division\Data $helperCompanyDivision
     * @param \Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory $discountMapCollectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory $discountMapFactory,
        CollectionFactory $websiteCollectionFactory,
        Cart $cart,
        ItemFactory $itemFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        SerializerInterface $serializer,
        \Magento\Framework\Math\Random $mathRandom,
        \Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory $discountCollectionFactory,
        QuoteItemFactory $quoteItemFactory,
        ProductCategoryList $productCategory,
        Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\CompanyDivision\Helper\Division\Data $helperCompanyDivision,
        \Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory $discountMapCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->discountMapFactory = $discountMapFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->cart = $cart;
        $this->itemFactory = $itemFactory;
        $this->quoteRepository = $quoteRepository;
        $this->serializer = $serializer;
        $this->mathRandom = $mathRandom;
        $this->discountCollectionFactory = $discountCollectionFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->productCategory = $productCategory;
        $this->customerSession = $session;
        $this->storeManager = $storeManager;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCompanyDivision = $helperCompanyDivision;
        $this->discountMapCollectionFactory = $discountMapCollectionFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return array
     */
    public function getWebsite()
    {
        $customer = $this->websiteCollectionFactory->create();
        $output = $customer->getData();
        $result = [];

        foreach ($output as $val) {
            $result[$val['website_id']] = $val['name'];
        }

        return $result;
    }

    /**
     * @param int $parentId
     * @param boolean $reset
     * @return array
     */
    public function getCustomerId($parentId, $reset)
    {
        $multipleDiscountCollection = $this->discountMapFactory->create()->getCollection();

        $output = $multipleDiscountCollection->getData();
        $result = [];

        foreach ($output as $val) {
            if ($reset && $parentId != $val['parent_id']) {
                $result[] = $val['customer_id'];
            } elseif (!$reset && $parentId == $val['parent_id']) {
                $result[] = $val['customer_id'];
            }
        }

        return $result;
    }

    public function updateLineItems($itemId, $quoteId, $secondProductQty, $deleteFlag = false)
    {
        $cartitems = $this->cart->getQuote()->getAllItems();
        foreach ($cartitems as $cartitem) {
            if ($cartitem->getitemId() == $itemId) {
                $cartitem->setquoteId($quoteId);
                $cartitem->setitemId($itemId);

                $requiredQty = $this->getFreeProductTotalRequiredQty($cartitem->getSku());

                if ($deleteFlag == true) {
                    $cartitem->setqty($cartitem->getQty() - $secondProductQty);
                } elseif ($cartitem->getQty() <= $requiredQty) {
                    $cartitem->setqty($requiredQty);
                } elseif ($cartitem->getQty() < $secondProductQty) {
                    $cartitem->setqty($secondProductQty);
                }
            }
        }
        return true;
    }

    public function setDiscountPrice()
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($additionalOption = $item->getOptionByCode('additional_options')) {
                $item->setCustomPrice(0);
                $item->setOriginalCustomPrice(0);
            }
        }
        return $items;
    }

    public function addLineItems(
        $discountTurner,
        $item,
        $product,
        $result,
        $secondProductQty,
        $firstProductQty,
        $requiredQty = 0
    ) {
        $customOptions['custom_discount'] = [
            'label' => 'Discount',
            'value' => $this->mathRandom->getRandomNumber(5, 10) . time()
        ];

        $itemQty = $item->getQty();
        $discountTurnerNumber = intval($itemQty / $firstProductQty);

        $product->addCustomOption('additional_options',
            $this->serializer->serialize($customOptions));

        if ($requiredQty) {
            $result = $result->getQuote()->addProduct($product, $requiredQty);
        } else {
            if ($discountTurnerNumber * $secondProductQty > $discountTurner * $secondProductQty) {
                $result = $result->getQuote()->addProduct($product,
                    $discountTurner * $secondProductQty);
            } else {
                $result = $result->getQuote()->addProduct($product,
                    $discountTurnerNumber * $secondProductQty);
            }
        }

        return $result;
    }

    public function sameProductDiscount($discountId, $websiteId)
    {
        $sameProductDiscount = $this->discountCollectionFactory->create()
            ->addFieldToFilter('id', $discountId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('start_date', ['lteq' => date('Y-m-d')])
            ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
            ->addFieldToFilter('discount_type', 0)
            ->addFieldToFilter('product_variation', 0)
            ->getData();

        return $sameProductDiscount;
    }

    public function differentProductDiscount($discountId, $websiteId)
    {
        $differentProductDiscount = $this->discountCollectionFactory->create()
            ->addFieldToFilter('id', $discountId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('start_date', ['lteq' => date('Y-m-d')])
            ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
            ->addFieldToFilter('discount_type', 0)
            ->addFieldToFilter('product_variation', 1)
            ->getData();

        return $differentProductDiscount;
    }

    public function cartLineItems(
        $item,
        $firstProductQty,
        $discountTurner,
        $quoteId,
        $sku,
        $secondProductQty,
        $product,
        $result,
        $totalFree
    ) {
        $discountTurnerNumber = intval($item->getQty() / $firstProductQty);
        $quoteItemCollection = $this->quoteItemFactory->create()
            ->addFieldToFilter('quote_id', $quoteId)
            ->addFieldToFilter('sku', $sku)
            ->addFieldToFilter('price', 0);

        if ($discountTurnerNumber > $discountTurner) {
            $discountTurnerNumber = $discountTurner;
        }

        if ($quoteItemCollection->getData()) {
            foreach ($quoteItemCollection as $key => $value) {
                $itemId = $value->getId();

                if ($value->getQty() < ($discountTurnerNumber * $secondProductQty) || $value->getQty() < $totalFree) {
                    $result = $this->updateLineItems($itemId,
                        $quoteId, ($discountTurnerNumber * $secondProductQty));
                }
            }
        } else {
            $result = $this->addLineItems($discountTurner, $item,
                $product, $result, $secondProductQty, $firstProductQty);
        }

        return $result;
    }

    public function brandDiscount($discountId, $websiteId)
    {
        $brandDiscount = $this->discountCollectionFactory->create()
            ->addFieldToFilter('id', $discountId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('start_date', ['lteq' => date('Y-m-d')])
            ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
            ->addFieldToFilter('discount_type', 0)
            ->addFieldToFilter('product_variation', 2)
            ->getData();

        return $brandDiscount;
    }

    public function requiredFreeQty($discountArray, $sku)
    {
        $cartitems = $this->cart->getQuote()->getAllItems();
        $freeItems = [];
        $totalQty = [];
        foreach ($cartitems as $cartitem) {
            if ($cartitem->getPrice() == 0) {
                $freeItems[$cartitem->getSku()] = $cartitem->getQty();
            } else {
                if (isset($discountArray[$cartitem->getSku()])) {
                    foreach ($discountArray[$cartitem->getSku()] as $key => $rule) {
                        $turnerValue = $cartitem->getQty() / $rule->getFirstProductQuantity();
                        $secondProductSku = $rule->getSecondProductSku();
                        if ($rule->getProductVariation() == 0) {
                            $secondProductSku = $rule->getFirstProductSku();
                        }

                        if ($turnerValue > $rule->getDiscountTurner()) {
                            $turnerValue = $rule->getDiscountTurner();
                        }

                        if (isset($totalQty[$secondProductSku])) {
                            $totalQty[$secondProductSku] += intval($rule->getSecondProductQuantity() * $turnerValue);
                        } else {
                            $totalQty[$secondProductSku] = intval($rule->getSecondProductQuantity() * $turnerValue);
                        }
                    }
                }
            }
        }
        $requiredQty = 0;

        if (isset($totalQty[$sku])) {
            return $totalQty[$sku];
        }

        return 0;
    }

    public function getBrandId($productId)
    {
        $categoryIds = $this->productCategory->getCategoryIds($productId);
        $category = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }

        $currentCategoryId = null;
        if (!empty($category)) {
            $currentCategoryId = max($category);
        }
        
        return $currentCategoryId;
    }

    public function getFreeProductTotalRequiredQty($sku)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerType == 3) {
            $parentCustomerId = $this->getDivisionParentCustomerId($customerId);
            $discountParentId = $this->getDiscountParentIdArray($parentCustomerId);

            $buyXGetYDiscount = $this->getBuyXGetYDiscountCollection($discountParentId,
                $websiteId);

            $discountArray = [];
            if (isset($buyXGetYDiscount)) {
                foreach ($buyXGetYDiscount as $key => $value) {
                    $discountArray[$value->getFirstProductSku()][$value->getId()] = $value;
                }
            }

            $requiredQtyForBuyXGetY = $this->requiredFreeQty($discountArray, $sku);
            return $requiredQtyForBuyXGetY;
        }
    }

    public function cartBrandLineItems(
        $item,
        $firstProductQty,
        $discountTurner,
        $quoteId,
        $sku,
        $secondProductQty,
        $product,
        $result,
        $totalFree
    ) {
        $discountTurnerNumber = intval($item->getQty() / $firstProductQty);
        $quoteItemCollection = $this->quoteItemFactory->create()
            ->addFieldToFilter('quote_id', $quoteId)
            ->addFieldToFilter('sku', $sku)
            ->addFieldToFilter('price', 0);

        if ($discountTurnerNumber > $discountTurner) {
            $discountTurnerNumber = $discountTurner;
        }

        if ($quoteItemCollection->getData()) {
            foreach ($quoteItemCollection as $key => $value) {
                $itemId = $value->getId();

                if ($value->getQty() < ($discountTurnerNumber * $secondProductQty)) {
                    $result = $this->updateBrandLineItems($itemId,
                        $quoteId, ($discountTurnerNumber * $secondProductQty));
                }
            }
        } else {
            $result = $this->addLineItemsForBrand($discountTurner, $item,
                $product, $result, $secondProductQty, $firstProductQty);
        }

        return $result;
    }

    public function addLineItemsForBrand(
        $discountTurner,
        $item,
        $product,
        $result,
        $secondProductQty,
        $firstProductQty
    ) {
        $customOptions['custom_discount'] = [
            'label' => 'Discount',
            'value' => $this->mathRandom->getRandomNumber(5, 10) . time()
        ];

        $itemQty = $item->getQty();
        $discountTurnerNumber = intval($itemQty / $firstProductQty);

        $product->addCustomOption('additional_options',
            $this->serializer->serialize($customOptions));

        if ($discountTurnerNumber * $secondProductQty > $discountTurner * $secondProductQty) {
            $result = $result->getQuote()->addProduct($product,
                $discountTurner * $secondProductQty);
        } else {
            $result = $result->getQuote()->addProduct($product,
                $discountTurnerNumber * $secondProductQty);
        }

        return $result;
    }

    public function updateBrandLineItems($itemId, $quoteId, $secondProductQty, $deleteFlag = false)
    {
        $cartitems = $this->cart->getQuote()->getAllItems();
        foreach ($cartitems as $cartitem) {
            if ($cartitem->getitemId() == $itemId) {
                $cartitem->setquoteId($quoteId);
                $cartitem->setitemId($itemId);

                if ($deleteFlag == true) {
                    $cartitem->setqty($cartitem->getQty() - $secondProductQty);
                } elseif ($cartitem->getQty() < $secondProductQty) {
                    $cartitem->setqty($secondProductQty);
                }
            }
        }

        return true;
    }

    public function removeDiscountRules(
        $currentItemDiscountId,
        $websiteId,
        $currentItemQty,
        $quoteId,
        $brandDiscountTurnerNumber,
        $brandSecondProductQty
    ) {
        // For same product discount
        $sameProductDiscount = $this->sameProductDiscount($currentItemDiscountId, $websiteId);

        // For different product discount
        $differentProductDiscount = $this->differentProductDiscount($currentItemDiscountId, $websiteId);

        $firstProductSku = null;
        $secondProductSku = null;
        $firstProductQty = null;
        $secondProductQty = null;
        $discountTurner = null;
        if (isset($sameProductDiscount[0])) {
            $firstProductSku = $sameProductDiscount[0]['first_product_sku'];
            $this->setFreeProduct($firstProductSku, $brandSecondProductQty, $brandDiscountTurnerNumber);

        } elseif (isset($differentProductDiscount[0])) {
            $secondProductSku = $differentProductDiscount[0]['second_product_sku'];
            $firstProductQty = $differentProductDiscount[0]['first_product_quantity'];
            $secondProductQty = $differentProductDiscount[0]['second_product_quantity'];
            $discountTurner = $differentProductDiscount[0]['discount_turner'];
            $discountTurnerNumber = intval($currentItemQty / $firstProductQty);

            if ($discountTurnerNumber > $discountTurner) {
                $discountTurnerNumber = $discountTurner;
            }
            $this->removeFreeProduct($secondProductSku, $secondProductQty, $discountTurnerNumber, $quoteId);
        }
    }

    public function removeFreeProduct($freeProductSku, $secondProductQty, $discountTurnerNumber, $quoteId)
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getPrice() == 0 && $item->getSku() == $freeProductSku
                && $item->getOptionByCode('additional_options')) {

                $freeProductId = $item->getId();
                if (($secondProductQty * $discountTurnerNumber) == $item->getQty()) {
                    $this->cart->getQuote()->removeItem($freeProductId);

                } elseif (($secondProductQty * $discountTurnerNumber) < $item->getQty()) {
                    $this->updateLineItems($freeProductId, $quoteId,
                        ($discountTurnerNumber * $secondProductQty), true);
                }
            }
        }
    }

    public function setFreeProduct($freeProductSku, $secondProductQty, $discountTurnerNumber)
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getPrice() == 0 && $item->getSku() == $freeProductSku
                && $item->getOptionByCode('additional_options')) {

                $item->setQty($secondProductQty * $discountTurnerNumber);
            }
        }
    }

    public function getDivisionParentCustomerId($contactPersonId)
    {
        $checkParentContactPerson = $this->helperCompanyDivision->isParentContact($contactPersonId);

        $parentRuleApplied = 0;
        if ($checkParentContactPerson) {
            $customerDetail = $this->helperContactPerson->getCustomerId($contactPersonId);

            $currentCustomerId = $this->customerSession->getCurrentCustomerId();
            if (isset($currentCustomerId)) {
                $customerId = $this->getParentRuleConfiguredCustomerId($currentCustomerId);
            } else {
                $customerId = $customerDetail['customer_id'];
            }

        } else {
            $customerDetail = $this->helperContactPerson->getCustomerId($contactPersonId);

            // For division specific discount
            $divisionCustomerId = $customerDetail['customer_id'];
            $customerId = $this->getParentRuleConfiguredCustomerId($divisionCustomerId);
        }

        return $customerId;
    }

    public function getDiscountParentIdArray($customerId)
    {
        $discountMapCollection = $this->discountMapCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getData();

        $discountParentId = [];
        if (isset($discountMapCollection)) {
            foreach ($discountMapCollection as $key => $value) {
                $discountParentId[] = $value['parent_id'];
            }
        }

        return $discountParentId;
    }

    public function setCurrentItemDiscountId($currentItem, $discountId)
    {
        $currentItem->setDiscountId($discountId);
        $currentItem->save();
    }

    public function getQuoteItemCollection($quoteId, $freeProductSku, $totalFree)
    {
        $quoteItemCollection = $this->quoteItemFactory->create()
            ->addFieldToFilter('quote_id', $quoteId)
            ->addFieldToFilter('sku', $freeProductSku)
            ->addFieldToFilter('qty', array('eq' => $totalFree))
            ->addFieldToFilter('price', 0);

        return $quoteItemCollection;
    }

    public function getBuyXGetYDiscountCollection($discountParentId, $websiteId)
    {
        $buyXGetYDiscountCollection = $this->discountCollectionFactory->create()
            ->addFieldToFilter('id', array('in' => array($discountParentId)))
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('start_date', ['lteq' => date('Y-m-d')])
            ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
            ->addFieldToFilter('discount_type', 0);

        return $buyXGetYDiscountCollection;
    }

    public function setDiscountTurnerNumberValue($discountTurnerNumber, $discountTurner)
    {
        if ($discountTurnerNumber > $discountTurner) {
            $discountTurnerNumber = $discountTurner;
        }

        return $discountTurnerNumber;
    }

    public function checkAndRemoveDiscountRules(
        $currentItemQty,
        $brandProductQty,
        $discountTurner,
        $currentItemDiscountId,
        $websiteId,
        $quoteId,
        $secondProductQty
    ) {

        $discountTurnerNumberValue = intval($currentItemQty / $brandProductQty);
        $discountTurnerNumber = $this->setDiscountTurnerNumberValue($discountTurnerNumberValue,
            $discountTurner);

        $this->removeDiscountRules($currentItemDiscountId,
            $websiteId, $currentItemQty, $quoteId,
            $discountTurnerNumber, $secondProductQty);
    }

    public function getFreeSku($buyXGetYDiscount)
    {
        $freeSku = [];
        if (isset($buyXGetYDiscount)) {
            foreach ($buyXGetYDiscount as $key => $value) {
                $secondProductSku = $value['second_product_sku'];
                $secondProductQty = $value['second_product_quantity'];
                $discountTurner = $value['discount_turner'];

                if ($value['product_variation'] == 0) {
                    $secondProductSku = $value['first_product_sku'];
                }

                if ($discountTurner == null) {
                    $discountTurner = 1;
                } else {
                    if ($discountTurner == 0) {
                        continue;
                    }
                }

                if (isset($freeSku[$secondProductSku])) {
                    $freeSku[$secondProductSku] += ($secondProductQty * $discountTurner);
                } else {
                    $freeSku[$secondProductSku] = ($secondProductQty * $discountTurner);
                }
            }
        }
        return $freeSku;
    }

    public function setItemPriceZero($item)
    {
        $item->setCustomPrice(0);
        $item->setOriginalCustomPrice(0);
    }

    public function getCartProductSkuArray()
    {
        $cartItemsSkuArray = [];
        $freeProductSkuArray = [];

        $cartItems = $this->cart->getQuote()->getAllItems();
        foreach ($cartItems as $item) {
            $cartItemsSkuArray[] = $item->getSku();
            $additionalOption = $item->getOptionByCode('additional_options');
            if ($additionalOption) {
                $freeProductSkuArray[] = $item->getSku();
            }
        }

        return ['cartItemsSkuArray' => $cartItemsSkuArray, 'freeProductSkuArray' => $freeProductSkuArray];
    }

    public function getParentRuleConfiguredCustomerId($divisionCustomerId)
    {
        $customer = $this->customerRepository->getById($divisionCustomerId);
        if ($customer->getCustomAttribute('parent_rule_configuration') == null) {
            $parentRuleApplied = 0;
        } else {
            $parentRuleApplied = $customer->getCustomAttribute('parent_rule_configuration')->getValue();
        }

        $mainCustomerId = $this->helperCompanyDivision->getMainCustomerId($divisionCustomerId);

        if ($parentRuleApplied) {
            $customerId = $mainCustomerId;
        } else {
            $customerId = $divisionCustomerId;
        }

        return $customerId;
    }
}