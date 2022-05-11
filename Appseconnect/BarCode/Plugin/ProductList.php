<?php

namespace Appseconnect\BarCode\Plugin;

class ProductList
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout
    )
    {
        $this->layout = $layout;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    )
    {
        $result = $proceed($product);
        return $result.$this->layout->createBlock('Magento\Framework\View\Element\Template')
                ->setProduct($product)
                ->setTemplate('Appseconnect_BarCode::product/sku.phtml')->toHtml();

    }
}
