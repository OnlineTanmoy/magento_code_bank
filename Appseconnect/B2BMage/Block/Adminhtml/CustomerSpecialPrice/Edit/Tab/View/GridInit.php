<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View;

/**
 * Abstract Class GridInit
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class GridInit extends \Magento\Backend\Block\Template
{

    /**
     * Block grid
     *
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    public $blockGrid;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * GridInit constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice customer special price helper
     * @param \Magento\Backend\Block\Template\Context                $context                    context
     * @param \Magento\Framework\Registry                            $registry                   registry
     * @param \Magento\Framework\Json\EncoderInterface               $jsonEncoder                json encode
     * @param array                                                  $data                       data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Prepare HTML
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::customerspecialprice/grid-init.phtml");
        
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
                    'Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View\ProductGrid',
                    'specialprice.product.grid'
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
     * Get product json
     *
     * @return string
     */
    public function getProductsJson()
    {
        $specialPriceId = $this->getRequest()->getParam('id');
        
        $specialPriceProducts = $this->helperCustomerSpecialPrice->getSpecialPriceProducts($specialPriceId);
        $products = [];
        foreach ($specialPriceProducts as $item) {
            $products[$item->getProductId()] = $item->getSpecialPrice();
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
