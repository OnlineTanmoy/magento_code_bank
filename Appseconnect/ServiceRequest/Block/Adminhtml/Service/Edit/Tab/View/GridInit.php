<?php
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View;

class GridInit extends \Magento\Backend\Block\Template
{

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    public $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->helperPricelist = $helperPricelist;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::pricelist/grid-init.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()
                ->createBlock(
                    'Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\ProductGrid',
                    'price.product.grid'
                );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     *
     * @return string
     */
    public function getProductsJson()
    {
        $pricelistId = $this->getRequest()->getParam('id');
        
        $pricelistProducts = $this->helperPricelist->getPricelistProducts($pricelistId);
        $products = [];
        foreach ($pricelistProducts as $item) {
            $products[$item->getProductId()] = $item->getFinalPrice();
        }
        
        if (! empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }

    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getCategory()
    {
        return $this->registry->registry('mymodule_item');
    }
}
