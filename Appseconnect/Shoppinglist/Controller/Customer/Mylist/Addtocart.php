<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class Addtocart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var customerProductListItem
     */
    protected $customerProductListItem;

    /**
     * @var messageManager
     */
    protected $messageManager;

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
     * @param Context $context
     */

    protected $storeManager;
    public function __construct(
        Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $product,
        StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    )
    {
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->quotationHelper = $quotationHelper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->quotationHelper->isFromQuote()) {
            $this->messageManager->addError(__('Please remove quote from cart'));

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('shoppinglist/customer/account_mylist');
            return $resultRedirect;
        }
        $postData = $this->getRequest()->getParams();        

        if (!empty($postData)) {
            $listItemCollection = $this->customerProductListItem->create()->getCollection()
                ->addFieldToFilter('list_id', array('eq' => $postData['list_id']));

            foreach ($listItemCollection as $item) {
                if (!isset($postData['product_type'])) {
                    $postData['product_type'] = null;
                }
                $productId = empty($postData['product_id']) ? $item->getProductId() : $postData['product_id'];
                $qty = empty($postData['qty']) ? $item->getQty() : $postData['qty'];
                if ($postData['product_type'] != null) {
                    parse_str(empty($postData['product_addtocart_data']) ? $item->getProductAddtocartData() : $postData['product_addtocart_data'],
                        $cartData);
                    $cartData['qty'] = $qty;
                    $params = $cartData;
                } else {
                    $params = array(
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $productId,
                        'qty' => $qty
                    );
                }

                $product = $this->product->getById($productId);
                $this->cart->addProduct($product, $params);
                if (!empty($postData['product_id'])) {
                    break;
                }
            }
            $this->cart->save();
            $this->messageManager->addSuccess(__('Successfully added the list item in the cart.'));
            $this->_redirect('checkout/cart/');
        }
    }
}
