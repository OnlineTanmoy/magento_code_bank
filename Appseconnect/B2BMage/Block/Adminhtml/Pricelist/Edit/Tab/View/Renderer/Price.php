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
class Price extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{

    /**
     * Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Type config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    public $typeConfig;

    /**
     * Price constructor.
     *
     * @param \Magento\Backend\Block\Context                      $context        Context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig     TypeConfig
     * @param \Magento\Catalog\Model\ProductFactory               $productFactory product factory
     * @param array                                               $data           Data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->typeConfig = $typeConfig;
        $this->productFactory = $productFactory;
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
            $finalPrice = $row->getData(
                $this->getColumn()
                    ->getIndex()
            );
            $factor = $this->getColumn()
                ->getFactor();
            if ($finalPrice == '') {
                $product = $this->productFactory->create()->load($row->getData('entity_id'));
                $finalPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue() * $factor;
                $originalPrice = $finalPrice;
            } else {
                $product = $this->productFactory->create()->load($row->getData('entity_id'));
                $originalPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue() * $factor;
            }
        }

        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" data_price="' . $originalPrice . '" ';
        $html .= 'value="' . $finalPrice . '" ' . $disabled;
        $html .= 'class="input-text admin__control-text ' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
