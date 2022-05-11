<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;

class Addcart extends \Magento\Framework\App\Action\Action
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
    public function __construct(
        Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $product
    )
    {
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
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
        if ($postData['list_id']) {

            $listItemCollection = $this->customerProductListItem->create()->getCollection()
                ->addFieldToFilter('list_id', array('eq' => $postData['list_id']));

            foreach ($listItemCollection as $item) {
                if($item->getProductType() != null) {
                    parse_str($item->getProductAddtocartData(), $cartData);
                    $cartData['qty'] = $item->getQty();
                    $params = $cartData;
                } else {
                    $params = array(
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $item->getProductId(), //product Id
                        'qty' => $item->getQty() //quantity of product
                    );
                }

                //Load the product based on productID
                $product = $this->product->getById($item->getProductId());
                $this->cart->addProduct($product, $params);

            }
            $this->cart->save();
            $this->messageManager->addSuccess(__('Successfully added the list item in the cart.'));
            $this->_redirect('checkout/cart/index');
        }
    }
}
