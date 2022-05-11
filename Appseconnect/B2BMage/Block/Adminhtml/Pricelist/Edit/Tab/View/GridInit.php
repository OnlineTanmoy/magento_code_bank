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
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View;

/**
 * Class GridInit
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
     * Product
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
     * EncoderInterface
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * GridInit constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist HelperPricelist
     * @param \Magento\Backend\Block\Template\Context     $context         Context
     * @param \Magento\Framework\Registry                 $registry        Registry
     * @param \Magento\Framework\Json\EncoderInterface    $jsonEncoder     JsonEncoder
     * @param array                                       $data            Data
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
     * Tohtml
     *
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
     * GetProductsJson
     *
     * @return string
     */
    public function getProductsJson()
    {
        $pricelistId = $this->getRequest()->getParam('id');
        
        $pricelistProducts = $this->helperPricelist->getPricelistProducts($pricelistId);
        $products = [];
        foreach ($pricelistProducts as $item) {
            $products[$item->getProductId()] = $item->getFinalPrice().'__'.$item->getIsManual();
        }
        
        if (! empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }

    /**
     * GetProductsJson
     *
     * @return string
     */
    public function getDiscountType()
    {
        $pricelistId = $this->getRequest()->getParam('id');

        $pricelist = $this->helperPricelist->pricelistModel->load($pricelistId);

        //return $pricelist->getDiscountType();
        return 1;
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
