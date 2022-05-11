<?php

namespace Appseconnect\Shoppinglist\Block\Customer\Account\Mylist;


use Magento\Customer\Model\Session;

class ItemList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var listCollection
     */
    public $listCollection;
    public $productRepository;
    /**
     * @var customerProductList
     */
    protected $customerProductList;
    /**
     * @var customerProductListItem
     */
    protected $customerProductListItem;
    /**
     * @var customerProductListItem
     */
    protected $productloader;
    /**
     * @var cartHelper
     */
    protected $cartHelper;
    /**
     * @var int
     */
    protected $listId;
    /**
     * @var \Magento\Bundle\Model\Selection
     */
    protected $bundleSelection;
    /**
     * @var bundleOption
     */
    protected $bundleOption;
    /**
     * @var formKey
     */
    protected $formKey;
    /**
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    protected $priceRuleData;
    /**
     * @var configurable
     */
    protected $configurable;
    protected $_image;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Bundle\Model\Selection $bundleSelection,
        \Magento\Bundle\Model\Option $bundleOption,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $priceRuleData,
        Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Catalog\Helper\Image $_image,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    ) {
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->bundleSelection = $bundleSelection;
        $this->productloader = $productloader;
        $this->bundleOption = $bundleOption;
        $this->configurable = $configurable;
        $this->formKey = $formKey;
        $this->priceRuleData = $priceRuleData;
        $this->customerSession = $customerSession;
        $this->_image = $_image;
        $this->productRepository = $productRepository;

        parent::__construct($context, $data);

        $this->productloader = $productloader;
        $this->cartHelper = $cartHelper;


        if ($this->getRequest()->getParam('id')) {

            $listCollection = $this->customerProductList->create()->getCollection()
                ->addFieldTofilter('entity_id', $this->getRequest()->getParam('id'))
                ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId());
            if (count($listCollection) > 0) {
                $this->listId = $this->getRequest()->getParam('id');
            } else {
                if ($this->getRequest()->getActionName() == 'account_editlist') {
                    $redirect->redirect($response, 'shoppinglist/customer/account_mylist');
                }
            }

        } else {
            if (isset($data['listId'])) {
                $this->listId = $data['listId'];
            }
        }

        if ($this->listId) {

            $collection = $this->customerProductListItem->create()->getCollection();
            $collection->addFieldToFilter('list_id', $this->listId);

            if (isset($data['searchData']) && $data['searchData'] != "") {
                $_collection = clone $collection;
                $collection->addFieldToFilter('product_sku', array('like' => '%' . $data['searchData'] . '%'));

                if (!$collection->count()) {
                    $collection = $_collection;
                    $collection->addFieldToFilter('product_description',
                        array('like' => '%' . $data['searchData'] . '%'));
                }
            }

            $collection->setOrder('product_sku', 'ASC');
            $this->setCollection($collection);
        } else {
            $this->listId = 0;
        }

        //get collection of data
        $this->pageConfig->getTitle()->set(__(''));
    }


    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    // method for get pager html
    public function getAttributeLabel($list, $attributeName, $optionId)
    {
        $_product = $this->productloader->create()->load($list->getProductId());
        $_attributeId = $_product->getResource()->getAttribute($attributeName);
        if ($_attributeId->usesSource()) {
            $_optionText = $_attributeId->getSource()->getOptionText($optionId);
        }
        return $_optionText;
    }

    /**
     * GetProductName
     * @param $sku
     * @return mixed
     */
    public function getProductName($sku)
    {
        return $this->productRepository->get($sku)->getName();
    }

    public function getProductImageUrl($id)
    {
        $product = $this->productloader->create()->load($id);

        return $this->_image->init($product, 'cart_page_product_thumbnail')->constrainOnly(false)
            ->keepAspectRatio(true)
            ->keepFrame(false)
            ->resize(30, 30)
            ->getUrl();
    }

    public function getProductUrl($id)
    {
        $product = $this->productloader->create()->load($id);
        return $product->getProductUrl();
    }

    public function getAddToCartProductUrl($productList)
    {
        $product = $this->productloader->create()->load($productList->getProductId());

        return $this->cartHelper->getAddUrl($product);
    }

    public function getAddToCartData($listMapId)
    {
        $listMap = $this->customerProductListItem->create()->load($listMapId);
        $addtocartData = $listMap->getProductAddtocartData();
        parse_str($addtocartData, $cartData);
        $cartData['qty'] = $listMap->getQty();
        $cartData['form_key'] = $this->formKey->getFormKey();
        $strOption = http_build_query($cartData);

        return $strOption;
    }

    public function getOrderUrl()
    {
        return $this->getUrl('shoppinglist/customer/mylist_addcart/', array('list_id' => $this->listId));
    }

    public function getProductOption($listMapId)
    {
        $listMap = $this->customerProductListItem->create()->load($listMapId);
        $productOption = $listMap->getProductOption();
        parse_str($productOption, $optionData);

        $str = '';
        if ($listMap->getProductType() == 'bundle') {
            asort($optionData['bundle_option']);
            $optionArray = [];
            foreach ($optionData['bundle_option'] as $optionId => $selection) {
                if (!in_array($optionId, $optionArray)) {
                    $bundleOption = $this->bundleOption->load($optionId);
                    $optionArray[] = $optionId;
                    $str .= '<div class="bundle-option">' . $bundleOption->getTitle() . '</div>';
                }
                $bundleSlection = $this->bundleSelection->load($selection);
                $product = $this->productloader->create()->load($bundleSlection->getProductId());
                $str .= '<div class="bundle-selection">  - ' . $product->getSku() . '</div>';
            }
        } else {
            if ($listMap->getProductType() == 'configurable') {
                $productParent = $this->productloader->create()->load($listMap->getProductId());
                $productAttributeOptions = $this->configurable->getConfigurableAttributesAsArray($productParent);
                foreach ($productAttributeOptions as $key => $value) {

                    $tmp_option = $value['values'];
                    if (count($tmp_option) > 0) {
                        $str .= '<div class="configurable-option">' . $value['label'] . ': ';

                        foreach ($tmp_option as $tmp) {
                            if (in_array($tmp['value_index'], $optionData['super_attribute'])) {
                                $str .= $tmp['label'] . "</div>";
                            }
                        }
                    }
                }
            }
        }

        return $str;
    }

    /**
     * @param $listMapId
     */
    public function getItemPrice($listMapId)
    {

        try {
            $listMap = $this->customerProductListItem->create()->load($listMapId);

            $productOption = $listMap->getProductOption();
            parse_str($productOption, $productData);

            $product = $this->productloader->create()->load($listMap->getProductId());
            $unitPrice = $this->priceRuleData->getFinalProductPrice($product);

            if ($listMap->getProductType() == 'configurable') {
                $unitPrice = 0;
                $childProduct = $this->configurable->getProductByAttributes($productData['super_attribute'], $product);
                $unitPrice += $this->priceRuleData->getFinalProductPrice($childProduct);
            } else {
                if ($listMap->getProductType() == 'bundle') {
                    $unitPrice = 0;
                    foreach ($productData['bundle_option'] as $optionId => $selectionId) {
                        if ($selectionId) {
                            $bundleSlection = $this->bundleSelection->load($selectionId);
                            $simpleProduct = $this->productloader->create()->load($bundleSlection->getProductId());
                            $unitPrice += number_format($this->priceRuleData->getFinalProductPrice($simpleProduct), 2);
                        }
                    }
                } else {
                    if ($listMap->getProductType() == 'grouped') {
                        $unitPrice = 0;
                        foreach ($productData['super_group'] as $productId => $simpleQty) {
                            if ($simpleQty > 0) {
                                $simpleProduct = $this->productloader->create()->load($productId);
                                $unitPrice += number_format($this->priceRuleData->getFinalProductPrice($simpleProduct),
                                        2) * $simpleQty;
                            }
                        }
                    }
                }
            }

        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            $unitPrice = 0;
        } catch (\Exception $e) {
            $unitPrice = 0;
        }

        return $unitPrice;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getCollection()) {
            if ($this->getRequest()->getParam('limit')) {
                $limit = $this->getRequest()->getParam('limit');
            } else {
                $limit = 10;
            }
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'shoppingmylistitem.grid.itemlist.pager'
            )->setLimit($limit)
                ->setCollection(
                    $this->getCollection() // assign collection to pager
                );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }
}
