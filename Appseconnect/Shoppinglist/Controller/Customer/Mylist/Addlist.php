<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\Price\Collection as PricelistPriceCollection;
use Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data as SpecialPriceHelper;
use Magento\Store\Model\StoreManagerInterface;

class Addlist extends \Magento\Customer\Controller\AbstractAccount
{
    protected $productrepository;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var customerProductList
     */
    protected $customerProductList;

    /**
     * @var customerProductListItem
     */
    protected $customerProductListItem;

    /**
     * @var messageManager
     */
    protected $messageManager;

    /**
     * @var productloader
     */
    protected $productloader;

    /**
     * @var configurable
     */
    protected $configurable;

    /**
     * @var bundleSelection
     */
    protected $bundleSelection;

    /**
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    protected $priceRuleData;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var PricelistPriceCollection
     */
    public $pricelistCollection;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * @var \Appseconnect\B2BMage\Helper\CategoryDiscount\Data
     */
    public $helperCategory;

    /**
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;

    /**
     * @var SpecialPriceHelper
     */
    public $helperCustomerSpecialPrice;

    /**
     * @var myListHelper
     */
    public $myListHelper;

    protected $storeManager;

    public $customerProductListCollectionFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Bundle\Model\Selection $bundleSelection,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $priceRuleData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PricelistPriceCollection $pricelistCollection,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        SpecialPriceHelper $helperCustomerSpecialPrice,
        \Appseconnect\Shoppinglist\Helper\Mylist\Data $myListHelper,
        StoreManagerInterface $storeManager,
        \Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductList\CollectionFactory $customerProductListCollectionFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->productrepository = $productrepository;
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
        $this->productloader = $productloader;
        $this->configurable = $configurable;
        $this->bundleSelection = $bundleSelection;
        $this->priceRuleData = $priceRuleData;
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->pricelistCollection = $pricelistCollection;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPricelist = $helperPricelist;
        $this->myListHelper = $myListHelper;
        $this->storeManager = $storeManager;
        $this->customerProductListCollectionFactory = $customerProductListCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postData = $_REQUEST;

        if(isset($postData['product'])){

            foreach ($postData as $key => $productDetailsArray) {
                foreach ($productDetailsArray as $index => $productDetails) {

                    $listName = '';
                    if (isset($productDetails['list_name'])) {
                        $listName = $productDetails['list_name'];

                        $listCollection = $this->customerProductListCollectionFactory->create()
                            ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())
                            ->addFieldToFilter('list_name', $listName)
                            ->addFieldToSelect('entity_id')
                            ->getFirstItem()
                            ->getData();

                        if (!empty($listCollection)) {
                            $productDetails['list_id'] = $listCollection['entity_id'];
                        } else {
                            $listData = $this->customerProductList->create();
                            $listData->setListName($listName)
                                ->setCustomerId($this->customerSession->getCustomerId())
                                ->setCreatedAt(date('Y-m-d H:i:s'))->save();

                            $productDetails['list_id'] = $listData->getId();
                        }
                    }

                    if (isset($productDetails['list_id'])) {

                        $list = $this->customerProductList->create()->load($productDetails['list_id']);

                        $product = $this->productloader->create()->load($productDetails['product_id']);

                        $unitPrice = $this->myListHelper->getFinalProductPrice($product);

                        if (!isset($productDetails['qty'])) {
                            $productDetails['qty'] = 1;
                        }

                        $strOption = '';

                        if (isset($productDetails['data_all'])) {
                            parse_str($productDetails['data_all'], $productData);
                            foreach ($productData as $key => $addData) {
                                if (in_array($key, array('related_product', 'form_key', 'qty'))) {
                                    unset($productData[$key]);
                                }
                            }

                            if ($productDetails['product_type'] == 'configurable') {
                                $unitPrice = 0;
                                $childProduct = $this->configurable->getProductByAttributes($productData['super_attribute'], $product);
                                $unitPrice += $this->myListHelper->getFinalProductPrice($childProduct);
                            }
                            else if ($productDetails['product_type'] == 'bundle') {
                                $unitPrice = 0;
                                foreach ($productData['bundle_option'] as $optionId => $selectionId) {
                                    if ($selectionId) {
                                        $bundleSlection = $this->bundleSelection->load($selectionId);
                                        $simpleProduct = $this->productloader->create()->load($bundleSlection->getProductId());
                                        $unitPrice += number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2);
                                    }
                                }
                            }
                            else if ($productDetails['product_type'] == 'grouped') {
                                $unitPrice = 0;
                                foreach ($productData['super_group'] as $productId => $simpleQty) {
                                    if ($simpleQty > 0) {
                                        $simpleProduct = $this->productloader->create()->load($productId);
                                        $unitPrice += number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2) * $simpleQty;
                                        $this->createSimpleList($productDetails, $productId, $simpleQty, number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2), $list, $productDetails);
                                    }
                                }
                            }

                            $strOption = http_build_query($productData);
                        }

                        if ($productDetails['product_type'] != 'grouped') {

                            $listItemCollection = $this->customerProductListItem->create()->getCollection()
                                ->addFieldToFilter('list_id', array('eq' => $productDetails['list_id']))
                                ->addFieldToFilter('product_id', array('eq' => $productDetails['product_id']));

                            if (isset($productDetails['product_type']) && $productDetails['product_type'] != 'simple') {
                                $listItemCollection->addFieldToFilter('product_type', $productDetails['product_type'])
                                    ->addFieldToFilter('product_option', $strOption);
                            } else {
                                $listItemCollection->addFieldToFilter('product_type', array('null' => true));
                            }

                            if ($listItemCollection->count() <= 0) {
                                $listItem = $this->customerProductListItem->create();
                                $listItem->setProductId($productDetails['product_id'])
                                    ->setProductSku($product->getSku())
                                    ->setProductDescription($product->getName())
                                    ->setProductUom($product->getProductUom())
                                    ->setListId($productDetails['list_id'])
                                    ->setQty($productDetails['qty'])
                                    ->setUnitPrice($unitPrice)
                                    ->setTotalPrice($unitPrice * $productDetails['qty']);

                                if (isset($productDetails['product_type']) && $productDetails['product_type'] != 'simple') {
                                    $listItem->setProductAddtocartData($productDetails['data_all']);
                                    $listItem->setProductType($productDetails['product_type']);
                                    $listItem->setProductOption($strOption);
                                }

                                $listItem->save();

                                $list->setItem($list->getItem() + 1)
                                    ->setTotalPrice($list->getTotalPrice() + ($unitPrice * $productDetails['qty']))
                                    ->save();
                            } else {
                                $listItem = $listItemCollection->getFirstItem();
                                $listItem
                                    ->setTotalPrice(($listItem->getUnitPrice() * ($listItem->getQty() + $productDetails['qty'])))
                                    ->setQty($listItem->getQty() + $productDetails['qty']);
                                if (isset($productDetails['product_type']) && $productDetails['product_type'] != 'simple') {
                                    $listItem->setProductAddtocartData($productDetails['data_all']);
                                    $listItem->setProductType($productDetails['product_type']);
                                    $listItem->setProductOption($strOption);
                                }
                                $listItem->save();

                                $list->setTotalPrice($list->getTotalPrice() + ($listItem->getUnitPrice() * $productDetails['qty']))
                                    ->save();
                            }
                        }

                        echo 'This product was added to your list.';
                        $storeUrl = $this->storeManager->getStore()->getBaseUrl();
                        $shoppingUrl = $storeUrl."shoppinglist/customer/account_mylist/";
                        $link = "<a href='".$shoppingUrl."'>";
                        $link .= "Shopping List</a>";
                        $this->messageManager->addSuccess(__('This product was added to your '.$link));
                    }
                    else {
                        echo 'Please select list.';
                    }
                }
            }

        } else {

            if(isset($postData['list_name'])){
                $listData = $this->customerProductList->create();
                $listData->setListName($postData['list_name'])
                    ->setCustomerId($this->customerSession->getCustomerId())
                    ->setCreatedAt(date('Y-m-d H:i:s'))->save();

                $postData['list_id'] = $listData->getId();
            }
            if (isset($postData['list_id'])) {

                $list = $this->customerProductList->create()->load($postData['list_id']);

                $product = $this->productloader->create()->load($postData['product_id']);

                $unitPrice = $this->myListHelper->getFinalProductPrice($product);

                if (!isset($postData['qty'])) {
                    $postData['qty'] = 1;
                }

                $strOption = '';
                if (isset($postData['data_all'])) {
                    parse_str($postData['data_all'], $productData);
                    foreach ($productData as $key => $addData) {
                        if (in_array($key, array('related_product', 'form_key', 'qty'))) {
                            unset($productData[$key]);
                        }
                    }

                    if ($postData['product_type'] == 'configurable') {
                        $unitPrice = 0;
                        $childProduct = $this->configurable->getProductByAttributes($productData['super_attribute'], $product);
                        $unitPrice += $this->myListHelper->getFinalProductPrice($childProduct);
                    }
                    else if ($postData['product_type'] == 'bundle') {
                        $unitPrice = 0;
                        foreach ($productData['bundle_option'] as $optionId => $selectionId) {
                            if ($selectionId) {
                                $bundleSlection = $this->bundleSelection->load($selectionId);
                                $simpleProduct = $this->productloader->create()->load($bundleSlection->getProductId());
                                $unitPrice += number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2);
                            }
                        }
                    }
                    else if ($postData['product_type'] == 'grouped') {
                        $unitPrice = 0;
                        foreach ($productData['super_group'] as $productId => $simpleQty) {
                            if ($simpleQty > 0) {
                                $simpleProduct = $this->productloader->create()->load($productId);
                                $unitPrice += number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2) * $simpleQty;
                                $this->createSimpleList($postData, $productId, $simpleQty, number_format($this->myListHelper->getFinalProductPrice($simpleProduct), 2), $list, $postData);
                            }
                        }
                    }

                    $strOption = http_build_query($productData);
                }

                if ($postData['product_type'] != 'grouped') {
                    $listItemCollection = $this->customerProductListItem->create()->getCollection()
                        ->addFieldToFilter('list_id', array('eq' => $postData['list_id']))
                        ->addFieldToFilter('product_id', array('eq' => $postData['product_id']));

                    if (isset($postData['product_type']) && $postData['product_type'] != 'simple') {
                        $listItemCollection->addFieldToFilter('product_type', $postData['product_type'])
                            ->addFieldToFilter('product_option', $strOption);
                    } else {
                        $listItemCollection->addFieldToFilter('product_type', array('null' => true));
                    }

                    if ($listItemCollection->count() <= 0) {
                        $listItem = $this->customerProductListItem->create();
                        $listItem->setProductId($postData['product_id'])
                            ->setProductSku($product->getSku())
                            ->setProductDescription($product->getName())
                            ->setProductUom($product->getProductUom())
                            ->setListId($postData['list_id'])
                            ->setQty($postData['qty'])
                            ->setUnitPrice($unitPrice)
                            ->setTotalPrice($unitPrice * $postData['qty']);

                        if (isset($postData['product_type']) && $postData['product_type'] != 'simple') {
                            $listItem->setProductAddtocartData($postData['data_all']);
                            $listItem->setProductType($postData['product_type']);
                            $listItem->setProductOption($strOption);
                        }

                        $listItem->save();

                        $list->setItem($list->getItem() + 1)
                            ->setTotalPrice($list->getTotalPrice() + ($unitPrice * $postData['qty']))
                            ->save();
                    } else {
                        $listItem = $listItemCollection->getFirstItem();
                        $listItem
                            ->setTotalPrice(($listItem->getUnitPrice() * ($listItem->getQty() + $postData['qty'])))
                            ->setQty($listItem->getQty() + $postData['qty']);
                        if (isset($postData['product_type']) && $postData['product_type'] != 'simple') {
                            $listItem->setProductAddtocartData($postData['data_all']);
                            $listItem->setProductType($postData['product_type']);
                            $listItem->setProductOption($strOption);
                        }
                        $listItem->save();

                        $list->setTotalPrice($list->getTotalPrice() + ($listItem->getUnitPrice() * $postData['qty']))
                            ->save();
                    }
                }

                echo 'This product was added to your list.';
                $storeUrl = $this->storeManager->getStore()->getBaseUrl();
                $shoppingUrl = $storeUrl."shoppinglist/customer/account_mylist/";
                $link = "<a href='".$shoppingUrl."'>";
                $link .= "Shopping List</a>";
                $this->messageManager->addSuccess(__('This product was added to your '.$link));
            }
            else {
                echo 'Please select list.';
            }
            
        }

    }

    public function createSimpleList($postData, $productId, $simpleQty, $unitPrice, $list, $allData)
    {

        $product = $this->productloader->create()->load($productId);
        $listItemCollection = $this->customerProductListItem->create()->getCollection()
            ->addFieldToFilter('list_id', array('eq' => $postData['list_id']))
            ->addFieldToFilter('product_id', array('eq' => $allData['product_id']));


        parse_str($allData['data_all'], $productData);
        $preProductData = $productData;
        foreach ($productData as $key => $addData) {
            if (in_array($key, array('related_product', 'form_key', 'qty'))) {
                unset($productData[$key]);
            }
        }
        $productData['super_group'] = array($productId => $simpleQty);
        $preProductData['super_group'] = array($productId => $simpleQty);
        $strOption = http_build_query($productData);

        if (isset($allData['product_type']) && $allData['product_type'] != 'simple') {
            $listItemCollection->addFieldToFilter('product_type', $allData['product_type'])
                ->addFieldToFilter('product_option', $strOption);
        }

        if ($listItemCollection->count() <= 0) {
            $listItem = $this->customerProductListItem->create();
            $listItem->setProductId($allData['product_id'])
                ->setProductSku($product->getSku())
                ->setProductDescription($product->getName())
                ->setProductUom($product->getProductUom())
                ->setListId($postData['list_id'])
                ->setQty($simpleQty)
                ->setUnitPrice($unitPrice)
                ->setTotalPrice($unitPrice * $simpleQty);

            $preStrOption = http_build_query($preProductData);

            $listItem->setProductAddtocartData($preStrOption);
            $listItem->setProductType($allData['product_type']);
            $listItem->setProductOption($strOption);

            $listItem->save();

            $list->setItem($list->getItem() + 1)
                ->setTotalPrice($list->getTotalPrice() + ($unitPrice * $simpleQty))
                ->save();
        } else {
            $listItem = $listItemCollection->getFirstItem();
            $preProductData['super_group'] = array($productId => $listItem->getQty() + $simpleQty);
            $listItem
                ->setTotalPrice(($listItem->getUnitPrice() * ($listItem->getQty() + $simpleQty)))
                ->setQty($listItem->getQty() + $simpleQty);

            $preStrOption = http_build_query($preProductData);

            $listItem->setProductAddtocartData($preStrOption);
            $listItem->setProductType($allData['product_type']);
            $listItem->setProductOption($strOption);

            $listItem->save();

            $list->setTotalPrice($list->getTotalPrice() + ($listItem->getUnitPrice() * $simpleQty))
                ->save();
        }
    }

}
