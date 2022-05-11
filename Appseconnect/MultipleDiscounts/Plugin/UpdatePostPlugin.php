<?php

namespace Appseconnect\MultipleDiscounts\Plugin;

class UpdatePostPlugin
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    public $cart;

    public $redirectFactory;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->cart = $cart;
        $this->redirectFactory = $redirectFactory;
    }
    /**
     * afterExecute
     *
     * @param \Magento\Checkout\Controller\Cart\UpdatePost $subject
     * @param $result
     */
    public function afterExecute(\Magento\Checkout\Controller\Cart\UpdatePost $subject, $result)
    {

        if ($this->cart->getCheckoutSession()->getCurrentReorder()) {
            return $this->redirectFactory->create()->setPath('checkout/cart');
        } else {
            return $result;
        }
    }
}