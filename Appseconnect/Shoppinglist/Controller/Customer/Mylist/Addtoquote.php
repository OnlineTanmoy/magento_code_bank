<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;

class Addtoquote extends \Magento\Framework\App\Action\Action
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
     * @var \Appseconnect\B2BMage\Model\CustomCart
     */
    public $customCart;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $product,
        \Appseconnect\B2BMage\Model\CustomCart $customCart
    )
    {
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->customCart = $customCart;
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

        if (!empty($postData)) {
            $listItemCollection = $this->customerProductListItem->create()->getCollection()
                ->addFieldToFilter('list_id', array('eq' => $postData['list_id']));

            foreach ($listItemCollection as $item) {
                $productId = empty($postData['product_id']) ? $item->getProductId() : $postData['product_id'];
                $qty = empty($postData['qty']) ? $item->getQty() : $postData['qty'];
                if ($item->getProductType() != null) {
                    parse_str($item->getProductAddtocartData(), $cartData);
                    $cartData['qty'] = $qty;
                    $params = $cartData;
                } else {
                    $params = array(
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $item->getProductId(), //product Id
                        'qty' => $item->getQty() //quantity of product
                    );
                }

                //Load the product based on productID
                $_product = $this->product->getById($productId);
                $this->customCart->addQuoteProduct($_product, $qty);
                if (!empty($postData['product_id'])) {
                    break;
                }
            }

            $this->customCart->save();

            $this->messageManager->addSuccess(__('Successfully added the list item in the quote.'));
            $this->_redirect('shoppinglist/customer/account_editlist/', array('id' => $postData['list_id']));
        }
    }
}
