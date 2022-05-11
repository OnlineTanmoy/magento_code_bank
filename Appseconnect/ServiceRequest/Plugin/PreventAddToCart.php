<?php
namespace Appseconnect\ServiceRequest\Plugin;

use Magento\Checkout\Model\Cart;

class PreventAddToCart
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    public $resultRedirectFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null)
    {
        $quote = $subject->getQuote();
        $items = $quote->getAllItems();

        foreach($items as $item) {
            if($item->getIsVirtual()) {

            }
        }

        if ($productInfo->getTypeId() === 'virtual') {
            $subject->truncate();
        }


        return [$productInfo,$requestInfo];
    }

    public function beforeUpdateItems(Cart $subject, $data)
    {
        $quote = $subject->getQuote();
        $items = $quote->getAllItems();
        foreach($items as $item) {
            if($item->getIsVirtual()) {

            }
        }

        return [$data];
    }
}
