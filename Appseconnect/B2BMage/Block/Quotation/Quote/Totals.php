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
namespace Appseconnect\B2BMage\Block\Quotation\Quote;

use Appseconnect\B2BMage\Model\Quote;

/**
 * Interface Totals
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * Associated array of totals
     * array(
     * $totalCode => $totalObject
     * )
     *
     * @var array
     */
    public $totals;

    /**
     * Quote
     *
     * @var Quote|null
     */
    public $quote = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Data object
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    public $dataObjectFactory;

    /**
     * Totals constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context           context
     * @param \Magento\Framework\DataObject\Factory            $dataObjectFactory data object
     * @param \Magento\Framework\Registry                      $registry          registry
     * @param array                                            $data              data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize self totals and children blocks totals before html building
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->_initTotals();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if (method_exists($child, 'initTotals') && is_callable(
                [
                $child,
                'initTotals'
                ]
            )
            ) {
                $child->initTotals();
            }
        }
        return parent::_beforeToHtml();
    }

    /**
     * Get quote object
     *
     * @return Quote
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            if ($this->hasData('quote')) {
                $this->quote = $this->_getData('quote');
            } elseif ($this->coreRegistry->registry('insync_current_customer_quote')) {
                $this->quote = $this->coreRegistry->registry('insync_current_customer_quote');
            } elseif ($this->getParentBlock()->getQuote()) {
                $this->quote = $this->getParentBlock()->getQuote();
            }
        }
        return $this->quote;
    }

    /**
     * Set quote
     *
     * @param Quote $quote quote
     *
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Quote
     */
    public function getSource()
    {
        return $this->getQuote();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    public function _initTotals()
    {
        $source = $this->getSource();
        
        $this->totals = [];
        $this->totals['subtotal'] = $this->dataObjectFactory->create(
            [
            'code' => 'subtotal',
            'value' => $source->getSubtotal(),
            'label' => __('Subtotal')
            ]
        );
        
        $this->totals['grand_total'] = $this->dataObjectFactory->create(
            [
            'code' => 'grand_total',
            'field' => 'grand_total',
            'strong' => true,
            'value' => $source->getGrandTotal(),
            'label' => __('Grand Total')
            ]
        );
        
        return $this;
    }

    /**
     * Add new total to totals array after specific total or before last total by default
     *
     * @param \Magento\Framework\DataObject $total total
     * @param null|string                   $after after
     *
     * @return $this
     */
    public function addTotal(\Magento\Framework\DataObject $total, $after = null)
    {
        if ($after !== null && $after != 'last' && $after != 'first') {
            $totals = [];
            $added = false;
            foreach ($this->totals as $code => $item) {
                $totals[$code] = $item;
                if ($code == $after) {
                    $added = true;
                    $totals[$total->getCode()] = $total;
                }
            }
            if (! $added) {
                $last = array_pop($totals);
                $totals[$total->getCode()] = $total;
                $totals[$last->getCode()] = $last;
            }
            $this->totals = $totals;
        } elseif ($after == 'last') {
            $this->totals[$total->getCode()] = $total;
        } elseif ($after == 'first') {
            $totals = [
                $total->getCode() => $total
            ];
            $this->totals = array_merge($totals, $this->totals);
        } else {
            $last = array_pop($this->totals);
            $this->totals[$total->getCode()] = $total;
            $this->totals[$last->getCode()] = $last;
        }
        return $this;
    }

    /**
     * Add new total to totals array before specific total or after first total by default
     *
     * @param \Magento\Framework\DataObject $total  total
     * @param null|string                   $before before
     *
     * @return $this
     */
    public function addTotalBefore(\Magento\Framework\DataObject $total, $before = null)
    {
        if ($before !== null) {
            if (! is_array($before)) {
                $before = [
                    $before
                ];
            }
            foreach ($before as $beforeTotals) {
                if (isset($this->totals[$beforeTotals])) {
                    $totals = [];
                    foreach ($this->totals as $code => $item) {
                        if ($code == $beforeTotals) {
                            $totals[$total->getCode()] = $total;
                        }
                        $totals[$code] = $item;
                    }
                    $this->totals = $totals;
                    return $this;
                }
            }
        }
        $totals = [];
        $first = array_shift($this->totals);
        $totals[$first->getCode()] = $first;
        $totals[$total->getCode()] = $total;
        foreach ($this->totals as $code => $item) {
            $totals[$code] = $item;
        }
        $this->totals = $totals;
        return $this;
    }

    /**
     * Get Total object by code
     *
     * @param string $code code
     *
     * @return mixed
     */
    public function getTotal($code)
    {
        if (isset($this->totals[$code])) {
            return $this->totals[$code];
        }
        return false;
    }

    /**
     * Delete total by specific
     *
     * @param string $code code
     *
     * @return $this
     */
    public function removeTotal($code)
    {
        unset($this->totals[$code]);
        return $this;
    }

    /**
     * Apply sort orders to totals array.
     * Array should have next structure
     * array(
     * $totalCode => $totalSortOrder
     * )
     *
     * @param array $order order
     *
     * @return $this @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applySortOrder($order)
    {
        return $this;
    }

    /**
     * Get totals array for visualization
     *
     * @param array|null $area area
     *
     * @return array
     */
    public function getTotals($area = null)
    {
        $totals = [];
        if ($area === null) {
            $totals = $this->totals;
        } else {
            $area = (string) $area;
            foreach ($this->totals as $total) {
                $totalArea = (string) $total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }

    /**
     * Format total value based on quote currency
     *
     * @param \Magento\Framework\DataObject $total total
     *
     * @return string
     */
    public function formatValue($total)
    {
        if (! $total->getIsFormated()) {
            return $this->getQuote()->formatPrice($total->getValue());
        }
        return $total->getValue();
    }
}
