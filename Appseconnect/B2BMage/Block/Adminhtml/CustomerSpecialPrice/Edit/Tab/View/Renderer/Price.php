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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Edit\Tab\View\Renderer;

/**
 * Abstract Class Price
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
     * Type config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    public $typeConfig;

    /**
     * Price constructor.
     *
     * @param \Magento\Backend\Block\Context                      $context    context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig type config
     * @param array                                               $data       data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->typeConfig = $typeConfig;
    }

    /**
     * Returns whether this qty field must be inactive
     *
     * @param \Magento\Framework\DataObject $row row
     *
     * @return bool
     */
    public function isRowInactive($row)
    {
        $typeId = $row->getTypeId();
        return $this->typeConfig->isProductSet($typeId);
    }

    /**
     * Render product qty field
     *
     * @param \Magento\Framework\DataObject $row row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $columnId = $this->getColumn()->getId();
        $disableIndicator = '';
        $addClassIndicator = '';
        
        if ($this->isRowInactive($row)) {
            $price = '';
            $disableIndicator = 'disabled="disabled" ';
            $addClassIndicator = ' input-inactive';
        } else {
            $price = $row->getData(
                $this->getColumn()
                    ->getIndex()
            );
            $price *= 1;
            if (! $price) {
                $price = '';
            }
        }
        
        $html = '<input type="text" ';
        $html .= 'name="' . $columnId . '" ';
        $html .= 'value="' . $price . '" ' . $disableIndicator;
        $html .= 'class="input-text admin__control-text ' .
            $this->getColumn()->getInlineCss() . $addClassIndicator . '" />';
        return $html;
    }
}
