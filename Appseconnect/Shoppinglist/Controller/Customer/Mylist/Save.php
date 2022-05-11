<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class Save extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Appseconnect\B2BMage\Model\CustomCart
     */
    public $customCart;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;
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
     * @var customerSession
     */
    protected $customerSession;
    /**
     * @var formKey
     */
    protected $formKey;
    /**
     * @var cart
     */
    protected $cart;
    /**
     * @var product
     */
    protected $product;
    /**
     * @var myListHelper
     */
    protected $myListHelper;
    /**
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    protected $priceRuleData;
    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Appseconnect\Shoppinglist\Helper\Mylist\Data $myListHelper,
        \Magento\Catalog\Model\ProductRepository $product,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $priceRuleData,
        Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Appseconnect\B2BMage\Model\CustomCart $customCart,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->myListHelper = $myListHelper;
        $this->priceRuleData = $priceRuleData;
        $this->quoteRepository = $quoteRepository;
        $this->customCart = $customCart;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postData = $this->getRequest()->getParams();

        $qtyAdd = array();
        $qtyIds = array();
        if (isset($postData['qty'])) {
            foreach ($postData['qty'] as $key => $val) {
                if ($val > 0) {
                    $qtyAdd[$key] = $val;
                    $qtyIds[] = $key;
                }
            }
        }

        if ($postData['mylist_save'] == 'save') {
            if ($postData['list_id']) {
                $list = $this->customerProductList->create()->load($postData['list_id']);

                $totalPrice = 0;
                if (isset($postData['qty']) && is_array($postData['qty'])) {
                    foreach ($postData['qty'] as $itemId => $qty) {
                        if ($qty == '' || $qty == null) {
                            $qty = 1;
                        }
                        $listItem = $this->customerProductListItem->create()->load($itemId);
                        $product = $this->productRepository->getById($listItem->getProductId());

                        if ($qty <= $product["quantity_and_stock_status"]['qty']) {
                            $listItem->setQty($qty)
                                ->setTotalPrice($qty * $listItem->getUnitPrice())
                                ->save();
                        } else {
                            $this->messageManager->addWarning(__('Requested Quantity is not available for ' . $product->getName()));
                        }

                        $totalPrice += $qty * $listItem->getUnitPrice();
                    }
                }
                $list->setListName($postData['list_name'])
                    ->setTotalPrice($totalPrice)
                    ->save();
            } else {
                $list = $this->customerProductList->create();
                $list->setListName($postData['list_name'])
                    ->setCustomerId($this->customerSession->getCustomerId())
                    ->setCreatedAt(date('Y-m-d H:i:s'))->save();

                if (isset($_FILES['b2b-mylist-file']['name']) && $_FILES['b2b-mylist-file']['name'] != '') {
                    $tmpName = $_FILES['b2b-mylist-file']['tmp_name'];
                    $csvAsArray = array_map('str_getcsv', file($tmpName));
                    if (count($csvAsArray) > 1) {
                        $count = 0;
                        foreach ($csvAsArray as $_item) {
                            if ($count == 0) {
                                $count++;
                                continue;
                            }
                            try {
                                $product = $this->product->get($_item[0]);
                                $unitPrice = $this->priceRuleData->getFinalProductPrice($product);

                                $qty = (isset($_item[1])) ? $_item[1] : 1;
                                $listItem = $this->customerProductListItem->create();
                                $listItem->setProductId($product->getId())
                                    ->setProductSku($product->getSku())
                                    ->setProductDescription($product->getName())
                                    ->setProductUom($product->getProductUom())
                                    ->setListId($list->getId())
                                    ->setQty($qty)
                                    ->setUnitPrice($unitPrice)
                                    ->setTotalPrice($unitPrice * $qty);

                                $listItem->save();
                                $list->setItem($list->getItem() + 1)
                                    ->setTotalPrice($list->getTotalPrice() + ($unitPrice * $qty))
                                    ->save();
                            } catch (NoSuchEntityException $e) {
                                $this->messageManager->addWarning(__('No such product entity for sku - ' . $_item[0]));
                            } catch (\Exception $ex) {
                                // no execution
                            }
                            $count++;
                        }
                    }
                }
            }


            $this->messageManager->addSuccess(__('Successfully saved the data.'));
            if (isset($postData['qty'])) {
                $this->_redirect('shoppinglist/customer/account_editlist/', array('id' => $postData['list_id']));
            } else {
                $this->_redirect('shoppinglist/customer/account_mylist/');
            }
        } else {
            if ($postData['mylist_save'] == 'cart') {
                if ($postData['list_id']) {
                    $listItemCollection = $this->customerProductListItem->create()->getCollection()
                        ->addFieldToFilter('list_id',
                            array('eq' => $postData['list_id']))->addFieldToFilter('entity_id', array('in' => $qtyIds));

                    foreach ($listItemCollection as $item) {
                        if ($item->getProductType() != null) {
                            parse_str($item->getProductAddtocartData(), $cartData);
                            $cartData['qty'] = $qtyAdd[$item->getId()];
                            $params = $cartData;
                        } else {
                            $params = array(
                                'form_key' => $this->formKey->getFormKey(),
                                'product' => $item->getProductId(), //product Id
                                'qty' => $item->getQty() //quantity of product
                            );
                        }
                        //Load the product based on productID
                        $_product = $this->product->getById($item->getProductId());
                        $this->cart->addProduct($_product, $params);

                    }
                    $this->cart->save();

                    $quoteId = $this->cart->getQuote()->getId();
                    $cartItems = $this->cart->getQuote()->getAllItems();
                    $quote = $this->quoteRepository->getActive($quoteId);
                    $quoteItems[] = $cartItems;
                    $this->quoteRepository->save($quote);
                    $quote->collectTotals();

                    $this->messageManager->addSuccess(__('Successfully added the list item in the cart.'));
                    $this->_redirect('checkout/cart/');
                }
            } else {
                if ($postData['mylist_save'] == 'quote') {
                    if ($postData['list_id']) {
                        $listItemCollection = $this->customerProductListItem->create()->getCollection()
                            ->addFieldToFilter('list_id',
                                array('eq' => $postData['list_id']))->addFieldToFilter('entity_id',
                                array('in' => $qtyIds));

                        foreach ($listItemCollection as $item) {
                            if ($item->getProductType() != null) {
                                parse_str($item->getProductAddtocartData(), $cartData);
                                $cartData['qty'] = $qtyAdd[$item->getId()];
                                $params = $cartData;
                            } else {
                                $params = array(
                                    'form_key' => $this->formKey->getFormKey(),
                                    'product' => $item->getProductId(), //product Id
                                    'qty' => $item->getQty() //quantity of product
                                );
                            }
                            //Load the product based on productID
                            $_product = $this->product->getById($item->getProductId());
                            $this->customCart->addQuoteProduct($_product, $item->getQty());
                        }
                        $this->customCart->save();

                        $this->messageManager->addSuccess(__('Successfully added the list item in the quote.'));
                        $this->_redirect('shoppinglist/customer/account_editlist/',
                            array('id' => $postData['list_id']));
                    }
                } else {
                    if ($postData['mylist_save'] == 'duplicate') {
                        if ($postData['list_id']) {
                            $listPrevious = $this->customerProductList->create()->load($postData['list_id']);

                            $list = $this->customerProductList->create();
                            $list->setListName($listPrevious->getListName() . '_copy')
                                ->setCustomerId($this->customerSession->getCustomerId())
                                ->setItem($listPrevious->getItem())
                                ->setTotalPrice($listPrevious->getTotalPrice())
                                ->setCreatedAt(date('Y-m-d H:i:s'))->save();

                            $listItemPreviousCollection = $this->customerProductListItem->create()->getCollection()
                                ->addFieldToFilter('list_id', array('eq' => $postData['list_id']));

                            foreach ($listItemPreviousCollection as $item) {
                                $listItem = $this->customerProductListItem->create();
                                $listItem->setProductId($item->getProductId())
                                    ->setListId($list->getId())
                                    ->setProductSku($item->getProductSku())
                                    ->setQty($item->getQty())
                                    ->setUnitPrice($item->getUnitPrice())
                                    ->setTotalPrice($item->getTotalPrice())
                                    ->setCreatedAt(date('Y-m-d H:i:s'))->save();

                            }

                            $this->messageManager->addSuccess(__('Successfully create the duplicate list. You can change the name of this list.'));
                            $this->_redirect('shoppinglist/customer/account_editlist/', array('id' => $list->getId()));
                        }
                    } else {
                        if ($postData['mylist_save'] == 'print') {
                            if ($postData['list_id']) {

                                $this->myListHelper->getListPrint($postData['list_id']);
                            }
                        } else {
                            if ($postData['mylist_save'] == 'share') {
                                if ($postData['list_id']) {
                                    $customerIds = explode(',', $_REQUEST['customer_id']);
                                    $this->myListHelper->addShareCustomer($customerIds, $postData['list_id']);
                                    $this->messageManager->addSuccess(__('Successfully shared this list.'));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
