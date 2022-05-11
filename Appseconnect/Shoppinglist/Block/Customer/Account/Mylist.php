<?php

namespace Appseconnect\Shoppinglist\Block\Customer\Account;

use Appseconnect\B2BMage\Block\ContactPerson\Contact\Listing;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class Mylist extends \Magento\Framework\View\Element\Template
{
    protected $customerProductList;

    /**
     * @var Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList
     */
    public $itemList;

    /**
     * @var \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory
     */
    public $customerProductListItem;

    public $customerSession;

    /**
     * @var cart
     */
    protected $cart;

    /**
     * @var myListHelper
     */
    protected $myListHelper;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList $itemList,
        Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Appseconnect\Shoppinglist\Helper\Mylist\Data $myListHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    )
    {
        $this->customerProductList = $customerProductList;
        $this->customerSession = $customerSession;
        $this->itemList = $itemList;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->myListHelper = $myListHelper;
        $this->productRepository = $productRepository;

        parent::__construct($context, $data);

        $this->pageConfig->getTitle()->set(__(''));
    }

    /**
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        if($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        } else {
            $limit = 10;
        }
        // create pager block for collection
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'shoppingmylist.grid.itemlist.pager'
        )->setLimit($limit)
            ->setCollection(
                $this->customerProductList->create()->getCollection()
                    ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId()) // assign collection to pager
            );
        $this->setChild('pager', $pager);// set pager block in layout

        return $this;
    }

    public function getCustomerProductList()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;

        $list = $this->customerProductList->create()->getCollection()
            ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId());

        $list->setPageSize($pageSize);
        $list->setCurPage($page);

        return $list->getData();
    }

    public function cartItems(){
        $cartItems = $this->cart->getQuote()->getAllItems();
        $itemDetais = array();
        foreach($cartItems as $item) {
            if($item->getParentItemId() == null){
                if($item->getProductType() == 'configurable'){
                    $product = $this->productRepository->getById($item->getProductId());
                    $superAttributeByChild = $this->myListHelper->getChildSuperAttribute($product->getSku(), $item->getSku());
                    $data_all = array(
                        'product'                       => $item->getProductId(),
                        'selected_configurable_option'  => '',
                        'related_product'               => '',
                        'item'                          => $item->getProductId(),
                        'form_key'                      => ''
                    );
                    foreach($superAttributeByChild as $key=>$attribute){
                        $data_all['super_attribute['.$key.']'] = $attribute;
                    }
                    $data_all['qty'] = 1;
                }else{
                    $data_all = array();
                }
                $itemDetais[] = array(
                    'product_id'    =>  $item->getProductId(),
                    'sku'           =>  $item->getSku(),
                    'qty'           =>  $item->getQty(),
                    'product_type'  =>  $item->getProductType(),
                    'data_all'      => http_build_query($data_all)
                );

            }
        }
        return json_encode($itemDetais);
    }

    public function customerLogin(){
        return $this->customerSession->isLoggedIn();
    }

    /**
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

}
