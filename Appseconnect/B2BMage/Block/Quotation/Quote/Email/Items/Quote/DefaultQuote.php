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
namespace Appseconnect\B2BMage\Block\Quotation\Quote\Email\Items\Quote;

use Appseconnect\B2BMage\Model\QuoteProduct as QuoteItem;

/**
 * Interface DefaultQuote
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DefaultQuote extends \Magento\Framework\View\Element\Template
{

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->getItem()->getQuote();
    }

    /**
     * Get item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        
        return $result;
    }

    /**
     * Get value html
     *
     * @param string|array $value value
     *
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf('%d', $value['qty']) . ' x ' . $this->escapeHtml($value['title']) . " " . $this->getItem()
                ->getQuote()
                ->formatPrice($value['price']);
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * Get sku
     *
     * @param mixed $item item
     *
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        } else {
            return $item->getProductSku();
        }
    }

    /**
     * Return product additional information block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get the html for item price
     *
     * @param QuoteItem $item item
     *
     * @return string
     */
    public function getItemPrice(QuoteItem $item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $block->setItem($item);
        return $block->toHtml();
    }
}
