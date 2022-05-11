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
 * Class Product
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{

    /**
     * Render product name to add Configure link
     *
     * @param \Magento\Framework\DataObject $row Row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $rendered = parent::render($row);
        $isConfigurable = $row->canConfigure();
        $style = $isConfigurable ? '' : 'disabled';
        $prodAttributes = $isConfigurable ?
                        sprintf('list_type = "product_to_add" product_id = %s', $row->getId())
                        : 'disabled="disabled"';
        return sprintf(
            '<a href="javascript:void(0)" class="action-configure %s" %s>%s</a>',
            $style,
            $prodAttributes,
            __('Configure')
        ) . $rendered;
    }
}
