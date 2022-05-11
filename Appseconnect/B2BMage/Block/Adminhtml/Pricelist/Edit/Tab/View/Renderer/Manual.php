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

namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\Renderer;

/**
 * Class Price
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Manual extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Helper Price list
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * Type config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    public $typeConfig;

    /**
     * Price constructor.
     *
     * @param \Magento\Backend\Block\Context                      $context         Context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig      TypeConfig
     * @param \Magento\Catalog\Model\ProductFactory               $productFactory  product factory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data         $helperPricelist helper pricelist
     * @param array                                               $data            Data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->typeConfig = $typeConfig;
        $this->productFactory = $productFactory;
        $this->helperPricelist = $helperPricelist;
    }

    /**
     * Returns whether this qty field must be inactive
     *
     * @param \Magento\Framework\DataObject $row Row
     *
     * @return bool
     */
    public function _isInactive($row)
    {
        $typeId = $row->getTypeId();
        return $this->typeConfig->isProductSet($typeId);
    }

    /**
     * Render product qty field
     *
     * @param \Magento\Framework\DataObject $row Row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $disabled = '';
        $addClass = '';

        if ($this->_isInactive($row)) {
            $qty = '';
            $disabled = 'disabled="disabled" ';
            $addClass = ' input-inactive';
        } else {

            $rowId = $this->getColumn()
                ->getName();

            $pricelistId = $row->getData('pricelist_id');

            if ($pricelistId) {
                $products = $this->helperPricelist->getPricelistProducts($pricelistId)
                    ->addFieldToFilter('product_id', $row->getData('entity_id'));
            } else {
                $products = $this->helperPricelist->pricelistProductCollectionFactory->create()
                    ->addFieldToFilter('product_pricelist_map_id', array('in' => $this->getColumn()->getValues()))
                    ->addFieldToFilter('product_id', $row->getData('entity_id'));
            }

            $checked = '';
            foreach ($products as $product) {
                if ($product->getIsManual()) {
                    $checked = 'checked';
                }
            }


        }

        $html = '<input type="checkbox" ';
        $html .= 'name="' . $rowId . '" ';
        $html .= 'value="' . 1 . '" ' . $checked . ' ';
        $html .= 'class="input-checkbox' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
