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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search\Grid\Renderer;

/**
 * Class Qty
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Qty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{

    /**
     * Type config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    public $typeConfig;
    
    /**
     * String
     *
     * @var string
     */
    public $disabled;
    
    /**
     * String
     *
     * @var string
     */
    public $addClass;
    
    /**
     * String
     *
     * @var string
     */
    public $html;

    /**
     * Qty constructor.
     *
     * @param \Magento\Backend\Block\Context                      $context    Context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig TypeConfig
     * @param array                                               $data       Data
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
     * @param \Magento\Framework\DataObject $row Row
     *
     * @return bool
     */
    public function isRowActive($row)
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
        $disabledIndicator = '';
        $addClassIndicator = '';
        
        if ($this->_isInactive($row)) {
            $qty = '';
            $disabledIndicator = 'disabled="disabled" ';
            $addClassIndicator = ' input-inactive';
        } else {
            $qty = $row->getData(
                $this->getColumn()
                    ->getIndex()
            );
            $qty *= 1;
            if (! $qty) {
                $qty = '';
            }
        }
        $columnId = $this->getColumn()->getId();
        // Compose html
        $html = '<input type="text" ';
        $html .= 'name="' . $columnId . '" ';
        $html .= 'value="' . $qty . '" ' . $disabledIndicator;
        $html .= 'class="input-text admin__control-text '
                . $this->getColumn()->getInlineCss() . $addClassIndicator . '" />';
        return $html;
    }
}
