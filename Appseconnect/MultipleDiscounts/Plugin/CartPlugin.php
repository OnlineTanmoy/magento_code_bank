<?php

namespace Appseconnect\MultipleDiscounts\Plugin;

use Magento\Customer\Model\Session;
use Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Catalog\Model\ProductCategoryList;

class CartPlugin
{
    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var CollectionFactory
     */
    public $discountMapCollectionFactory;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory
     */
    public $discountCollectionFactory;

    public $storeManager;

    public $productRepository;

    public $cart;

    /**
     * @var QuoteItemFactory
     */
    public $quoteItemFactory;

    /**
     * @var ItemFactory
     */
    public $itemFactory;

    public $quoteRepository;

    public $request;

    /**
     * @var \Appseconnect\MultipleDiscounts\Helper\Data
     */
    public $helperMultipleDiscount;

    public $messageManager;

    /**
     * @var ProductCategoryList
     */
    public $productCategory;

    public $helperCompanyDivision;

    public function __construct
    (
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        CollectionFactory $discountMapCollectionFactory,
        \Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory $discountCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Cart $cart,
        QuoteItemFactory $quoteItemFactory,
        ItemFactory $itemFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\App\Request\Http $request,
        \Appseconnect\MultipleDiscounts\Helper\Data $helperMultipleDiscount,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ProductCategoryList $productCategory,
        \Appseconnect\CompanyDivision\Helper\Division\Data $helperCompanyDivision
    ) {
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->discountMapCollectionFactory = $discountMapCollectionFactory;
        $this->discountCollectionFactory = $discountCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemFactory = $itemFactory;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
        $this->helperMultipleDiscount = $helperMultipleDiscount;
        $this->messageManager = $messageManager;
        $this->productCategory = $productCategory;
        $this->helperCompanyDivision = $helperCompanyDivision;
    }

    /**
     * afterAddProduct
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $result
     */
    public function afterAddProduct(\Magento\Checkout\Model\Cart $subject, $result)
    {
        $requestedProduct = $this->request->getParams();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerType == 3) {
            $parentCustomerId = $this->helperMultipleDiscount->getDivisionParentCustomerId($customerId);
            $discountParentId = $this->helperMultipleDiscount->getDiscountParentIdArray($parentCustomerId);

            if (empty($discountParentId)) {
                return $result;
            }

            // For brand discount
            if (isset($requestedProduct['product'])) {
                $quoteId = $result->getQuote()->getId();
                $brandDiscountIds = [];
                foreach ($discountParentId as $discountId) {
                    $brandDiscount = $this->helperMultipleDiscount->brandDiscount($discountId, $websiteId);

                    if (!empty($brandDiscount)) {
                        $brandId = null;
                        $brandProductQty = null;
                        $secondProductQty = null;
                        $discountTurner = null;

                        if (isset($brandDiscount[0])) {
                            $brandId = $brandDiscount[0]['brands'];
                            $brandProductQty = $brandDiscount[0]['first_product_quantity'];
                            $secondProductQty = $brandDiscount[0]['second_product_quantity'];
                            $discountTurner = $brandDiscount[0]['discount_turner'];

                            if (!in_array($brandId, $brandDiscountIds)) {
                                $brandDiscountIds[] = $brandId;
                            } else {
                                continue;
                            }

                            if ($discountTurner == null) {
                                $discountTurner = 1;
                            } else {
                                if ($discountTurner == 0) {
                                    continue;
                                }
                            }

                            $totalFree = $secondProductQty * $discountTurner;
                            $cartItems = $this->cart->getQuote()->getAllItems();
                            $productId = $requestedProduct['product'];
                            $currentCategoryId = $this->helperMultipleDiscount->getBrandId($productId);

                            if ($currentCategoryId == $brandId) {
                                foreach ($cartItems as $cartItem) {

                                    if ($cartItem->getPrice() != 0 && $cartItem->getQty() >= $brandProductQty) {
                                        $productId = $cartItem->getProductId();
                                        $categoryIds = $this->productCategory->getCategoryIds($productId);
                                        $category = [];
                                        if ($categoryIds) {
                                            $category = array_unique($categoryIds);
                                        }

                                        if (in_array($brandId, $category)) {
                                            if ($cartItem->getQty() <= $brandProductQty * $discountTurner) {
                                                $freeProductSku = $cartItem->getSku();
                                                $product = $this->productRepository->get($freeProductSku);

                                                // Get Current Item Discount Id
                                                $itemId = $cartItem->getId();
                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $currentItemDiscountId = $currentItem->getDiscountId();

                                                // Check for other discount rules and remove free product from other discounts
                                                if ($currentItemDiscountId > 0 && $currentItemDiscountId != $discountId) {
                                                    $currentItemQty = $cartItem->getQty();
                                                    $this->helperMultipleDiscount->checkAndRemoveDiscountRules(
                                                        $currentItemQty, $brandProductQty, $discountTurner,
                                                        $currentItemDiscountId, $websiteId, $quoteId, $secondProductQty
                                                    );
                                                }

                                                // Add Free product
                                                $result = $this->helperMultipleDiscount->cartBrandLineItems($cartItem,
                                                    $brandProductQty,
                                                    $discountTurner, $quoteId, $freeProductSku, $secondProductQty,
                                                    $product, $subject, $totalFree);

                                                // Set Current Item Discount Id
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    $discountId);
                                            } else {
                                                $quoteItemCollection = $this->helperMultipleDiscount
                                                    ->getQuoteItemCollection($quoteId, $cartItem->getSku(), $totalFree);

                                                if ($quoteItemCollection->getData()) {
                                                    continue;
                                                } else {
                                                    $freeProductSku = $cartItem->getSku();
                                                    $product = $this->productRepository->get($freeProductSku);

                                                    // Get Current Item Discount Id
                                                    $itemId = $cartItem->getId();
                                                    $currentItem = $subject->getQuote()->getItemById($itemId);
                                                    $currentItemDiscountId = $currentItem->getDiscountId();

                                                    // Check for other discount rules and remove free product from other discounts
                                                    if ($currentItemDiscountId > 0 && $currentItemDiscountId != $discountId) {
                                                        $currentItemQty = $cartItem->getQty();
                                                        $this->helperMultipleDiscount->checkAndRemoveDiscountRules(
                                                            $currentItemQty, $brandProductQty, $discountTurner,
                                                            $currentItemDiscountId, $websiteId, $quoteId,
                                                            $secondProductQty
                                                        );
                                                    }

                                                    // Add Free product
                                                    $result = $this->helperMultipleDiscount->cartBrandLineItems($cartItem,
                                                        $brandProductQty,
                                                        $discountTurner, $quoteId, $freeProductSku,
                                                        $secondProductQty,
                                                        $product, $subject, $totalFree);

                                                    // Set Current Item Discount Id
                                                    $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                        $discountId);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $this->helperMultipleDiscount->setDiscountPrice();
                        }
                    }
                }
            }

            // For bypassing products not included in x sku of any rules
            $buyXGetYDiscountCollection = $this->helperMultipleDiscount->getBuyXGetYDiscountCollection($discountParentId,
                $websiteId);
            $buyXGetYDiscount = $buyXGetYDiscountCollection->getData();

            $firstProductSkuArray = [];
            if (isset($buyXGetYDiscount)) {
                foreach ($buyXGetYDiscount as $key => $value) {
                    $firstProductSkuArray[] = $value['first_product_sku'];
                }
            }

            $requestedProductId = null;
            $requestedProductSku = null;
            if (isset($requestedProduct['product'])) {
                $requestedProductId = $requestedProduct['product'];
                $requestedProductSku = $this->productRepository->getById($requestedProductId)->getSku();
            }

            if (in_array($requestedProductSku, $firstProductSkuArray)) {
                $quote = $subject->getQuote()->getAllItems();
                $quoteId = $subject->getQuote()->getId();

                $freeSku = $this->helperMultipleDiscount->getFreeSku($buyXGetYDiscount);

                foreach ($discountParentId as $discountId) {

                    // For same product discount
                    $sameProductDiscount = $this->helperMultipleDiscount->sameProductDiscount($discountId,
                        $websiteId);

                    // For different product discount
                    $differentProductDiscount = $this->helperMultipleDiscount->differentProductDiscount($discountId,
                        $websiteId);

                    $firstProductSku = null;
                    $secondProductSku = null;
                    $firstProductQty = null;
                    $secondProductQty = null;
                    $discountTurner = null;

                    if (isset($sameProductDiscount[0])) {
                        $firstProductSku = $sameProductDiscount[0]['first_product_sku'];
                        $firstProductQty = $sameProductDiscount[0]['first_product_quantity'];
                        $secondProductQty = $sameProductDiscount[0]['second_product_quantity'];
                        $discountTurner = $sameProductDiscount[0]['discount_turner'];

                    } elseif (isset($differentProductDiscount[0])) {
                        $firstProductSku = $differentProductDiscount[0]['first_product_sku'];
                        $secondProductSku = $differentProductDiscount[0]['second_product_sku'];
                        $firstProductQty = $differentProductDiscount[0]['first_product_quantity'];
                        $secondProductQty = $differentProductDiscount[0]['second_product_quantity'];
                        $discountTurner = $differentProductDiscount[0]['discount_turner'];
                    }

                    if (empty($firstProductQty) || empty($secondProductQty)) {
                        continue;
                    }

                    if ($firstProductSku == $requestedProductSku) {
                        $product = null;

                        if (isset($sameProductDiscount[0]) && isset($firstProductSku)) {
                            $product = $this->productRepository->get($firstProductSku);
                        } elseif (isset($differentProductDiscount[0]) && isset($secondProductSku)) {
                            $product = $this->productRepository->get($secondProductSku);
                        }

                        if ($discountTurner == null) {
                            $discountTurner = 1;
                        } else {
                            if ($discountTurner == 0) {
                                continue;
                            }
                        }

                        foreach ($quote as $item) {

                            if (isset($requestedProductId) && $requestedProductId == $item->getProductId()) {
                                if (isset($sameProductDiscount[0]) && $secondProductQty != 0) {

                                    if (($item->getDiscountId() == null || $item->getDiscountId() == 0 || $item->getDiscountId() == $discountId)
                                        && $item->getPrice() != 0 && !$item->getOptionByCode('additional_options')) {

                                        if ($item->getQty() <= $discountTurner * $firstProductQty) {
                                            if ($firstProductSku == $item->getSku()
                                                && (($item->getQty() % $firstProductQty) == 0 || $item->getQty() > $firstProductQty)) {

                                                $result = $this->helperMultipleDiscount->cartLineItems($item,
                                                    $firstProductQty, $discountTurner, $quoteId, $firstProductSku,
                                                    $secondProductQty, $product, $subject, $freeSku[$firstProductSku]);

                                                $itemId = $item->getId();
                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    $discountId);
                                            }
                                        } else {
                                            if ($firstProductSku == $item->getSku()) {
                                                $quoteItemCollection = $this->helperMultipleDiscount
                                                    ->getQuoteItemCollection($quoteId, $firstProductSku,
                                                        $freeSku[$firstProductSku]);

                                                if ($quoteItemCollection->getData()) {
                                                    continue;
                                                } else {
                                                    $result = $this->helperMultipleDiscount->cartLineItems($item,
                                                        $firstProductQty, $discountTurner, $quoteId, $firstProductSku,
                                                        $secondProductQty, $product, $subject,
                                                        $freeSku[$firstProductSku]);

                                                    $itemId = $item->getId();
                                                    $currentItem = $subject->getQuote()->getItemById($itemId);
                                                    $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                        $discountId);
                                                }
                                            }
                                        }
                                    } else {
                                        continue;
                                    }

                                } elseif (isset($differentProductDiscount[0]) && $secondProductQty != 0 && !empty($secondProductSku)) {

                                    if (($item->getDiscountId() == null || $item->getDiscountId() == 0 || $item->getDiscountId() == $discountId)
                                        && $item->getPrice() != 0 && !$item->getOptionByCode('additional_options')) {

                                        if ($item->getQty() <= $discountTurner * $firstProductQty) {
                                            if ($firstProductSku == $item->getSku()
                                                && (($item->getQty() % $firstProductQty) == 0 || $item->getQty() > $firstProductQty)) {

                                                $result = $this->helperMultipleDiscount->cartLineItems($item,
                                                    $firstProductQty, $discountTurner, $quoteId, $secondProductSku,
                                                    $secondProductQty, $product, $subject, $freeSku[$secondProductSku]);

                                                $itemId = $item->getId();
                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    $discountId);
                                            }
                                        } else {
                                            if ($firstProductSku == $item->getSku()) {
                                                $quoteItemCollection = $this->helperMultipleDiscount
                                                    ->getQuoteItemCollection($quoteId, $secondProductSku,
                                                        $freeSku[$secondProductSku]);

                                                if ($quoteItemCollection->getData()) {
                                                    continue;
                                                } else {
                                                    $result = $this->helperMultipleDiscount->cartLineItems($item,
                                                        $firstProductQty, $discountTurner, $quoteId, $secondProductSku,
                                                        $secondProductQty, $product, $subject,
                                                        $freeSku[$secondProductSku]);

                                                    $itemId = $item->getId();
                                                    $currentItem = $subject->getQuote()->getItemById($itemId);
                                                    $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                        $discountId);
                                                }
                                            }
                                        }
                                    } else {
                                        continue;
                                    }
                                }
                            }
                        }
                        $this->helperMultipleDiscount->setDiscountPrice();
                    }
                }
                return $result;
            } else {
                return $result;
            }
        }
        return $result;
    }

    /**
     * beforeUpdateItems
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $data
     */
    public function beforeUpdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($additionalOption = $item->getOptionByCode('additional_options')) {
                if (isset($data[$item->getId()])) {
                    if ($data[$item->getId()]['qty'] != $item->getQty()) {
                        $this->messageManager->addErrorMessage(__("This item cannot be updated"));
                    }
                    unset($data[$item->getId()]);
                }
            }
        }

        return [$data];
    }

    /**
     * afterUpdateItems
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $result
     * @param $data
     */
    public function afterUpdateItems(\Magento\Checkout\Model\Cart $subject, $result, $data)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerType == 3) {
            $parentCustomerId = $this->helperMultipleDiscount->getDivisionParentCustomerId($customerId);
            $discountParentId = $this->helperMultipleDiscount->getDiscountParentIdArray($parentCustomerId);

            if (empty($discountParentId)) {
                return $result;
            }

            $buyXGetYDiscount = $this->helperMultipleDiscount->getBuyXGetYDiscountCollection($discountParentId,
                $websiteId);

            $firstProductSkuArray = [];
            $discountArray = [];
            $brandDiscountArray = [];
            if (isset($buyXGetYDiscount)) {
                foreach ($buyXGetYDiscount as $key => $value) {
                    $firstProductSkuArray[] = $value->getFirstProductSku();
                    $discountArray[$value->getFirstProductSku()][$value->getId()] = $value;
                    $brandDiscountArray[$value->getBrands()][$value->getId()] = $value;
                }
            }

            // For brand discount
            $brandDiscount = null;
            $brandDiscountIds = [];
            $quoteId = $subject->getQuote()->getId();

            foreach ($discountParentId as $discountId) {
                $brandDiscount = $this->helperMultipleDiscount->brandDiscount($discountId, $websiteId);

                if (!empty($brandDiscount)) {
                    $brandId = null;
                    $brandProductQty = null;
                    $secondProductQty = null;
                    $discountTurner = null;

                    if (isset($brandDiscount[0])) {
                        $brandId = $brandDiscount[0]['brands'];
                        $brandProductQty = $brandDiscount[0]['first_product_quantity'];
                        $secondProductQty = $brandDiscount[0]['second_product_quantity'];
                        $discountTurner = $brandDiscount[0]['discount_turner'];

                        if (!in_array($brandId, $brandDiscountIds)) {
                            $brandDiscountIds[] = $brandId;
                        } else {
                            continue;
                        }

                        $cartItems = $this->cart->getQuote()->getAllItems();
                        foreach ($data as $itemId => $itemInfo) {
                            $itemData = $this->itemFactory->create()->load($itemId);
                            $itemSku = $itemData->getSku();
                            $productId = $itemData->getProductId();
                            $currentCategoryId = $this->helperMultipleDiscount->getBrandId($productId);

                            if ($currentCategoryId == $brandId) {
                                if (empty($brandProductQty) || empty($secondProductQty)) {
                                    continue;
                                }

                                if ($discountTurner == null) {
                                    $discountTurner = 1;
                                } else {
                                    if ($discountTurner == 0) {
                                        continue;
                                    }
                                }

                                $discountTurnerNumberValue = intval($itemInfo['qty'] / $brandProductQty);
                                $discountTurnerNumber = $this->helperMultipleDiscount
                                    ->setDiscountTurnerNumberValue($discountTurnerNumberValue, $discountTurner);

                                $cartProductSkuArray = $this->helperMultipleDiscount->getCartProductSkuArray();
                                $cartItemsSkuArray = $cartProductSkuArray['cartItemsSkuArray'];
                                $freeProductSkuArray = $cartProductSkuArray['freeProductSkuArray'];

                                foreach ($cartItems as $item) {
                                    if (in_array($itemSku, $cartItemsSkuArray)) {
                                        if ($item->getPrice() == 0 && $item->getSku() == $itemSku) {
                                            $freeProductId = $item->getId();

                                            $this->helperMultipleDiscount->setItemPriceZero($item);

                                            if ($discountTurnerNumber == 0) {
                                                $this->cart->getQuote()->removeItem($freeProductId);
                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    0);
                                            } else {
                                                // Get Current Item Discount Id
                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $currentItemDiscountId = $currentItem->getDiscountId();

                                                // Check for other discount rules and remove free product from other discounts
                                                if ($currentItemDiscountId > 0 && $currentItemDiscountId != $discountId) {
                                                    $currentItemQty = $itemInfo['qty'];
                                                    $this->helperMultipleDiscount->checkAndRemoveDiscountRules(
                                                        $currentItemQty, $brandProductQty, $discountTurner,
                                                        $currentItemDiscountId, $websiteId, $quoteId, $secondProductQty
                                                    );
                                                }
                                                $item->setQty($secondProductQty * $discountTurnerNumber);

                                                // Set Current Item Discount Id
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    $discountId);
                                            }
                                        } else {
                                            if (!in_array($itemSku, $freeProductSkuArray)) {
                                                if ($itemSku == $item->getSku()
                                                    && (($itemInfo['qty'] % $brandProductQty) == 0 || $itemInfo['qty'] > $brandProductQty)) {

                                                    $xitem = $subject->getQuote()->getItemById($itemId);
                                                    $product = $this->productRepository->get($itemSku);

                                                    // Get Current Item Discount Id
                                                    $currentItem = $subject->getQuote()->getItemById($itemId);
                                                    $currentItemDiscountId = $currentItem->getDiscountId();

                                                    // Check for other discount rules and remove free product from other discounts
                                                    if ($currentItemDiscountId > 0 && $currentItemDiscountId != $discountId) {
                                                        $currentItemQty = $itemInfo['qty'];
                                                        $this->helperMultipleDiscount->checkAndRemoveDiscountRules(
                                                            $currentItemQty, $brandProductQty, $discountTurner,
                                                            $currentItemDiscountId, $websiteId, $quoteId,
                                                            $secondProductQty
                                                        );
                                                    }

                                                    // Add Free product
                                                    $this->helperMultipleDiscount->addLineItemsForBrand($discountTurner,
                                                        $xitem, $product, $subject, $secondProductQty,
                                                        $brandProductQty);

                                                    array_push($freeProductSkuArray, $itemSku);
                                                    $this->helperMultipleDiscount->setDiscountPrice();

                                                    // Set Current Item Discount Id
                                                    $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                        $discountId);
                                                }
                                            }
                                        }
                                    } elseif (!in_array($itemSku, $cartItemsSkuArray)) {
                                        if ($itemId == $item->getId()
                                            && $item->getPrice() != 0 && !$item->getOptionByCode('additional_options')
                                            && (($itemInfo['qty'] % $brandProductQty) == 0 || $itemInfo['qty'] > $brandProductQty)) {

                                            $xitem = $subject->getQuote()->getItemById($itemId);
                                            $product = $this->productRepository->get($itemSku);

                                            // Get Current Item Discount Id
                                            $currentItem = $subject->getQuote()->getItemById($itemId);
                                            $currentItemDiscountId = $currentItem->getDiscountId();

                                            // Check for other discount rules and remove free product from other discounts
                                            if ($currentItemDiscountId > 0 && $currentItemDiscountId != $discountId) {
                                                $currentItemQty = $itemInfo['qty'];
                                                $this->helperMultipleDiscount->checkAndRemoveDiscountRules(
                                                    $currentItemQty, $brandProductQty, $discountTurner,
                                                    $currentItemDiscountId, $websiteId, $quoteId, $secondProductQty
                                                );
                                            }

                                            // Add Free product
                                            $this->helperMultipleDiscount->addLineItemsForBrand($discountTurner,
                                                $xitem, $product, $subject, $secondProductQty, $brandProductQty);

                                            array_push($cartItemsSkuArray, $itemSku);
                                            $this->helperMultipleDiscount->setDiscountPrice();

                                            // Set Current Item Discount Id
                                            $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                $discountId);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // For Buy X Get Y discount
            foreach ($data as $itemId => $itemInfo) {
                $itemData = $this->itemFactory->create()->load($itemId);
                $itemSku = $itemData->getSku();
                $itemDiscountId = $itemData->getDiscountId();

                if (in_array($itemSku, $firstProductSkuArray)) {
                    foreach ($discountArray[$itemSku] as $ruleId => $rule) {

                        if ($itemDiscountId == null || $itemDiscountId == 0 || $itemDiscountId == $ruleId) {
                            if (empty($rule->getFirstProductQuantity()) || empty($rule->getSecondProductQuantity())) {
                                continue;
                            }

                            $discountTurner = $rule->getDiscountTurner();
                            if ($discountTurner == null) {
                                $discountTurner = 1;
                            } else {
                                if ($discountTurner == 0) {
                                    continue;
                                }
                            }

                            $discountTurnerNumberValue = intval($itemInfo['qty'] / $rule->getFirstProductQuantity());
                            $discountTurnerNumber = $this->helperMultipleDiscount
                                ->setDiscountTurnerNumberValue($discountTurnerNumberValue, $discountTurner);

                            $secondProductQty = $rule->getSecondProductQuantity();
                            $freeProductSku = $rule->getSecondProductSku();

                            if ($rule->getProductVariation() == 0) {
                                $freeProductSku = $rule->getFirstProductSku();
                            } else {
                                if (!$rule->getSecondProductSku() && $rule->getProductVariation()) {
                                    return $result;
                                }
                            }

                            $items = $this->cart->getQuote()->getAllItems();
                            $cartProductSkuArray = $this->helperMultipleDiscount->getCartProductSkuArray();
                            $cartItemsSkuArray = $cartProductSkuArray['cartItemsSkuArray'];
                            $freeProductSkuArray = $cartProductSkuArray['freeProductSkuArray'];
                            foreach ($items as $item) {
                                if (in_array($freeProductSku, $cartItemsSkuArray)) {
                                    if ($item->getPrice() == 0 && $item->getSku() == $freeProductSku) {
                                        $freeProductId = $item->getId();

                                        $this->helperMultipleDiscount->setItemPriceZero($item);

                                        if ($discountTurnerNumber == 0) {
                                            $requiredQty = $this->helperMultipleDiscount->requiredFreeQty($discountArray,
                                                $freeProductSku);

                                            if ($discountTurnerNumber == 0 && $requiredQty == 0) {
                                                $this->cart->getQuote()->removeItem($freeProductId);
                                            } elseif ($requiredQty) {
                                                $item->setQty($requiredQty);
                                            } elseif ($item->getQty() == ($secondProductQty * $discountTurner)) {
                                                $this->cart->getQuote()->removeItem($freeProductId);
                                            } else {
                                                $item->setQty($item->getQty() - ($secondProductQty * $discountTurner));
                                            }

                                            $currentItem = $subject->getQuote()->getItemById($itemId);
                                            $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem, 0);
                                        } else {
                                            $requiredQty = $this->helperMultipleDiscount->requiredFreeQty($discountArray,
                                                $freeProductSku);
                                            $item->setQty($requiredQty);

                                            $currentItem = $subject->getQuote()->getItemById($itemId);
                                            $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                $ruleId);
                                        }
                                    } else {
                                        if (!in_array($freeProductSku, $freeProductSkuArray) && $item->getDiscountId() == 0) {
                                            if ($freeProductSku == $item->getSku()
                                                && (($itemInfo['qty'] % $rule->getFirstProductQuantity()) == 0
                                                    || $itemInfo['qty'] > $rule->getFirstProductQuantity())) {

                                                $requiredQty = $this->helperMultipleDiscount->requiredFreeQty($discountArray,
                                                    $freeProductSku);

                                                $xitem = $subject->getQuote()->getItemById($itemId);
                                                $product = $this->productRepository->get($freeProductSku);

                                                $this->helperMultipleDiscount->addLineItems($discountTurner, $xitem,
                                                    $product, $subject, $secondProductQty,
                                                    $rule->getFirstProductQuantity(), $requiredQty);

                                                $this->helperMultipleDiscount->setDiscountPrice();

                                                $currentItem = $subject->getQuote()->getItemById($itemId);
                                                $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem,
                                                    $ruleId);
                                            }
                                        }
                                    }
                                } elseif (!in_array($freeProductSku, $cartItemsSkuArray) && $item->getDiscountId() == 0) {
                                    if ($rule->getFirstProductSku() == $item->getSku()
                                        && $item->getPrice() != 0 && !$item->getOptionByCode('additional_options')
                                        && (($itemInfo['qty'] % $rule->getFirstProductQuantity()) == 0
                                            || $itemInfo['qty'] > $rule->getFirstProductQuantity())) {

                                        $xitem = $subject->getQuote()->getItemById($itemId);
                                        $product = $this->productRepository->get($freeProductSku);

                                        $this->helperMultipleDiscount->addLineItems($discountTurner, $xitem,
                                            $product, $subject, $secondProductQty, $rule->getFirstProductQuantity());

                                        $this->helperMultipleDiscount->setDiscountPrice();

                                        $currentItem = $subject->getQuote()->getItemById($itemId);
                                        $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem, $ruleId);
                                    }
                                }
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
            $this->helperMultipleDiscount->setDiscountPrice();
        }
        return $result;
    }

    /**
     * beforeRemoveItem
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $itemId
     */
    public function beforeRemoveItem(\Magento\Checkout\Model\Cart $subject, $itemId)
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($itemId == $item->getId()) {
                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                    if (isset($itemId)) {
                        $this->messageManager->addErrorMessage(__("This item cannot be deleted"));
                        $itemId = 0;
                        break;
                    }
                }
            }
        }
        return [$itemId];
    }

    /**
     * afterRemoveItem
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $result
     * @param $itemId
     */
    public function afterRemoveItem(\Magento\Checkout\Model\Cart $subject, $result, $itemId)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerType == 3) {
            $parentCustomerId = $this->helperMultipleDiscount->getDivisionParentCustomerId($customerId);
            $discountParentId = $this->helperMultipleDiscount->getDiscountParentIdArray($parentCustomerId);

            if (empty($discountParentId)) {
                return $result;
            }

            $buyXGetYDiscount = $this->helperMultipleDiscount->getBuyXGetYDiscountCollection($discountParentId,
                $websiteId);

            $firstProductSkuArray = [];
            $discountArray = [];
            $brandDiscountArray = [];
            if (isset($buyXGetYDiscount)) {
                foreach ($buyXGetYDiscount as $key => $value) {
                    $firstProductSkuArray[] = $value->getFirstProductSku();
                    $discountArray[$value->getFirstProductSku()][$value->getId()] = $value;
                    $brandDiscountArray[$value->getBrands()][$value->getId()] = $value;
                }
            }

            $itemData = $this->itemFactory->create()->load($itemId);
            $itemSku = $itemData->getSku();
            $itemPrice = $itemData->getPrice();
            $itemQty = $itemData->getQty();
            $quoteId = $itemData->getQuoteId();
            $productId = $itemData->getProductId();
            $productBrandId = $this->helperMultipleDiscount->getBrandId($productId);

            if ($itemPrice != 0) {

                // For brand discount
                $brandDiscount = null;
                if (isset($brandDiscountArray[$productBrandId])) {
                    foreach ($brandDiscountArray[$productBrandId] as $ruleId => $rule) {
                        $brandDiscount = $this->helperMultipleDiscount->brandDiscount($ruleId, $websiteId);

                        if (!empty($brandDiscount)) {
                            $brandId = null;
                            $brandProductQty = null;
                            $secondProductQty = null;
                            $discountTurner = null;

                            if (isset($brandDiscount[0])) {
                                $brandId = $rule->getBrands();
                                $brandProductQty = $rule->getFirstProductQuantity();
                                $secondProductQty = $rule->getSecondProductQuantity();
                                $discountTurner = $rule->getDiscountTurner();

                                $cartItems = $this->cart->getQuote()->getAllItems();
                                $itemData = $this->itemFactory->create()->load($itemId);
                                $itemQty = $itemData->getQty();
                                $itemSku = $itemData->getSku();
                                $productId = $itemData->getProductId();
                                $currentCategoryId = $this->helperMultipleDiscount->getBrandId($productId);

                                if ($currentCategoryId == $brandId) {
                                    if (empty($brandProductQty) || empty($secondProductQty)) {
                                        continue;
                                    }

                                    if ($discountTurner == null) {
                                        $discountTurner = 1;
                                    } else {
                                        if ($discountTurner == 0) {
                                            continue;
                                        }
                                    }

                                    $discountTurnerNumberValue = intval($itemQty / $brandProductQty);
                                    $discountTurnerNumber = $this->helperMultipleDiscount
                                        ->setDiscountTurnerNumberValue($discountTurnerNumberValue, $discountTurner);

                                    foreach ($cartItems as $item) {
                                        if ($item->getPrice() == 0 && $item->getSku() == $itemSku) {
                                            $freeProductId = $item->getId();

                                            if (($secondProductQty * $discountTurnerNumber) == $item->getQty()) {
                                                $this->cart->getQuote()->removeItem($freeProductId);
                                            } elseif (($secondProductQty * $discountTurnerNumber) < $item->getQty()) {
                                                $this->helperMultipleDiscount->updateBrandLineItems($freeProductId,
                                                    $quoteId, ($discountTurnerNumber * $secondProductQty), true);
                                            }
                                        }
                                    }
                                    $currentItem = $subject->getQuote()->getItemById($itemId);
                                    $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem, 0);
                                }
                            }
                        }
                    }
                }

                $currentItem = $subject->getQuote()->getItemById($itemId);
                $itemDiscountId = $currentItem->getDiscountId();

                // For Buy X Get Y discount
                if (in_array($itemSku, $firstProductSkuArray)) {
                    foreach ($discountArray[$itemSku] as $ruleId => $rule) {
                        if ($itemDiscountId == null || $itemDiscountId == 0 || $itemDiscountId == $ruleId) {
                            if (empty($rule->getFirstProductQuantity()) || empty($rule->getSecondProductQuantity())) {
                                continue;
                            }

                            $discountTurner = $rule->getDiscountTurner();
                            if ($discountTurner == null) {
                                $discountTurner = 1;
                            } else {
                                if ($discountTurner == 0) {
                                    continue;
                                }
                            }

                            $discountTurnerNumberValue = intval($itemQty / $rule->getFirstProductQuantity());
                            $discountTurnerNumber = $this->helperMultipleDiscount
                                ->setDiscountTurnerNumberValue($discountTurnerNumberValue, $discountTurner);

                            $secondProductQty = $rule->getSecondProductQuantity();
                            $freeProductSku = $rule->getSecondProductSku();

                            if ($rule->getProductVariation() == 0) {
                                $freeProductSku = $rule->getFirstProductSku();
                            }

                            $items = $this->cart->getQuote()->getAllItems();
                            foreach ($items as $item) {
                                if ($item->getPrice() == 0 && $item->getSku() == $freeProductSku) {
                                    $freeProductId = $item->getId();

                                    if (($secondProductQty * $discountTurnerNumber) == $item->getQty()) {
                                        $this->cart->getQuote()->removeItem($freeProductId);
                                    } elseif (($secondProductQty * $discountTurnerNumber) < $item->getQty()) {
                                        $this->helperMultipleDiscount->updateLineItems($freeProductId,
                                            $quoteId, ($discountTurnerNumber * $secondProductQty), true);
                                    }
                                }
                            }
                            $currentItem = $subject->getQuote()->getItemById($itemId);
                            $this->helperMultipleDiscount->setCurrentItemDiscountId($currentItem, 0);
                        }
                    }
                }
            }
        }
        return $result;
    }
}