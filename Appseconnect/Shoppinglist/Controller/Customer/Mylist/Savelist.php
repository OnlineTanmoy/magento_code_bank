<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Savelist extends \Magento\Customer\Controller\AbstractAccount
{
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

    protected $helperData;

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
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperData
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
        $this->helperData = $helperData;
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
        if (isset($postData)) {

            $customerId = $this->customerSession->getCustomerId();
            if($this->customerSession->getCustomer()->getCustomerType() == 3){
                $customerId = $this->helperData->getCustomerIdFromContactPerson($this->customerSession->getCustomerId());
            }

            $list = $this->customerProductList->create();
            $listName = trim($postData['list_name']);
            $list->setListName($listName)
                ->setCustomerId($customerId)
                ->setCreatedAt(date('Y-m-d H:i:s'))->save();

            if (isset($postData['product_id']) && $postData['product_id'] != '') {
                try {
                    $product = $this->product->getById($postData['product_id']);
                    $unitPrice = $this->priceRuleData->getFinalProductPrice($product);

                    $qty = (isset($postData['qty'])) ? $postData['qty'] : 1;
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
                
                    echo 'This product was added to your newly created list "'.$listName.'".';
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addWarning(__('No such product entity for sku - ' . $product->getSku()));
                } catch (\Exception $ex) {
                    // no execution
                }
            } else {
                echo 'Please enter the list name.';
            }
        }
    }
}
