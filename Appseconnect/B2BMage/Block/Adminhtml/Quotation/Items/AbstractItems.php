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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Items;

use Appseconnect\B2BMage\Model\Quote;
use Magento\Sales\Model\Order\Creditmemo\Item;

/**
 * Class AbstractItems
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AbstractItems extends \Magento\Backend\Block\Template
{

    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Renderers for other column with column name key
     * block => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    public $columnRenders = [];

    /**
     * Flag - if it is set method canEditQty will return value of it
     *
     * @var bool|null
     */
    public $canEditQty;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * StockRegistryInterface
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * StockConfigurationInterface
     *
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    public $stockConfiguration;

    /**
     * AbstractItems constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                   $context            Context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface      $stockRegistry      StockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration StockConfiguration
     * @param \Magento\Framework\Registry                               $registry           Registry
     * @param array                                                     $data               Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->stockConfiguration = $stockConfiguration;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Add column renderers
     *
     * @param array $blocks Blocks
     *
     * @return $this
     */
    public function setColumnRenders(array $blocks)
    {
        foreach ($blocks as $blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block->getRenderedBlock() === null) {
                $block->setRenderedBlock($this);
            }
            $this->columnRenders[$blockName] = $block;
        }
        return $this;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type Type
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (! $renderer instanceof \Magento\Framework\View\Element\BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setColumnRenders(
            $this->getLayout()
                ->getGroupChildNames($this->getNameInLayout(), 'column')
        );
        
        return $renderer;
    }

    /**
     * Retrieve column renderer block
     *
     * @param string $column        Column
     * @param string $compositePart CompositePart
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getColumnRenderer($column, $compositePart = '')
    {
        $column = 'column_' . $column;
        if (isset($this->columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (! isset($this->columnRenders[$column])) {
            return false;
        }
        return $this->columnRenders[$column];
    }

    /**
     * Retrieve rendered item html content
     *
     * @param \Magento\Framework\DataObject $item Item
     *
     * @return string
     */
    public function getItemHtml(\Magento\Framework\DataObject $item)
    {
        $orderItem = $item->getOrderItem();
        if ($orderItem) {
            $type = $orderItem->getProductType();
        } else {
            $type = $item->getProductType();
        }
        
        return $this->getItemRenderer($type)
            ->setItem($item)
            ->setCanEditQty($this->canEditQty())
            ->toHtml();
    }

    /**
     * ######################### SALES ##################################
     */
    
    /**
     * Retrieve available quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Quote
     */
    public function getQuote()
    {
        if ($this->hasQuote()) {
            return $this->getData('quote');
        }
        if ($this->coreRegistry->registry('insync_current_customer_quote')) {
            return $this->coreRegistry->registry('insync_current_customer_quote');
        }
        if ($this->coreRegistry->registry('quote')) {
            return $this->coreRegistry->registry('quote');
        }
        
        throw new \Magento\Framework\Exception\LocalizedException(
            __('We can\'t get the quote instance right now.')
        );
    }

    /**
     * Retrieve price formatted html content
     *
     * @param float  $basePrice BasePrice
     * @param float  $price     Price
     * @param bool   $strong    Strong
     * @param string $separator Separator
     *
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />')
    {
        return $this->displayRoundedPrices($basePrice, $price, 2, $strong, $separator);
    }

    /**
     * Display base and regular prices with specified rounding precision
     *
     * @param float  $basePrice BasePrice
     * @param float  $price     Price
     * @param int    $precision Precision
     * @param bool   $strong    Strong
     * @param string $separator Separator
     *
     * @return string
     */
    public function displayRoundedPrices($basePrice, $price, $precision = 2, $strong = false, $separator = '<br />')
    {
        if ($this->getQuote()->isCurrencyDifferent()) {
            $res = '';
            $res .= $this->getQuote()->formatBasePricePrecision($basePrice, $precision);
            $res .= $separator;
            $res .= $this->getQuote()->formatPricePrecision($price, $precision, true);
        } else {
            $res = $this->getQuote()->formatPricePrecision($price, $precision);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        }
        return $res;
    }
}
