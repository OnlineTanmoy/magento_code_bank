<?php
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\Renderer;

class Price extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{

    /**
     * Type config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    public $typeConfig;

    /**
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig
     * @param array $data
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
     * @param \Magento\Framework\DataObject $row
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
     * @param \Magento\Framework\DataObject $row
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
            $qty = $row->getData($this->getColumn()
                ->getIndex());
            $qty *= 1;
            if (! $qty) {
                $qty = '';
            }
        }
        
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $qty . '" ' . $disabled;
        $html .= 'class="input-text admin__control-text ' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
