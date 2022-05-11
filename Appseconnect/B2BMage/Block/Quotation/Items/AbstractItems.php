<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Quotation\Items;

/**
 * Interface AbstractItems
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AbstractItems extends \Magento\Framework\View\Element\Template
{

    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Retrieve item renderer block
     *
     * @param string $type type
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \RuntimeException @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getItemRenderer($type)
    {
        $rendererList = $this->getRendererListName()
                        ? $this->getLayout()->getBlock($this->getRendererListName())
                        : $this->getChildBlock('renderer.list');
        if (! $rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }
        $overriddenTemplateFiles = $this->getOverriddenTemplates() ?: [];
        $template = isset($overriddenTemplateFiles[$type]) ? $overriddenTemplateFiles[$type] : $this->getRendererTemplate();
        $rendererElement = $rendererList->getRenderer($type, self::DEFAULT_TYPE, $template);
        $rendererElement->setRenderedBlock($this);
        return $rendererElement;
    }

    /**
     * Prepare item before output
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $renderer rendrer
     *
     * @return $this @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareItem(\Magento\Framework\View\Element\AbstractBlock $renderer)
    {
        return $this;
    }

    /**
     * Return product type for quote/order item
     *
     * @param \Magento\Framework\DataObject $item item
     *
     * @return string
     */
    public function getItemType(\Magento\Framework\DataObject $item)
    {
        return $item->getProductType();
    }

    /**
     * Get item row html
     *
     * @param \Magento\Framework\DataObject $item item
     *
     * @return string
     */
    public function getItemHtml(\Magento\Framework\DataObject $item)
    {
        $type = $this->getItemType($item);
        
        $block = $this->getItemRenderer($type)->setItem($item);
        $this->prepareItem($block);
        return $block->toHtml();
    }


}
