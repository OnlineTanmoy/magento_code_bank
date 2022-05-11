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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation;

/**
 * Class Totals
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Appseconnect\B2BMage\Block\Quotation\Quote\Totals
{

    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    public $adminHelper;

    /**
     * Factory
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    public $dataObjectFactory;

    /**
     * Totals constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context           Context
     * @param \Magento\Framework\DataObject\Factory            $dataObjectFactory DataObjectFactory
     * @param \Magento\Framework\Registry                      $registry          Registry
     * @param \Magento\Sales\Helper\Admin                      $adminHelper       AdminHelper
     * @param array                                            $data              Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->adminHelper = $adminHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $dataObjectFactory, $registry, $data);
    }

    /**
     * Format total value based on order currency
     *
     * @param \Magento\Framework\DataObject $total Total
     *
     * @return string
     */
    public function formatValue($total)
    {
        if (! $total->getIsFormated()) {
            return $this->adminHelper
                ->displayPrices($this->getQuote(), $total->getBaseValue(), $total->getValue());
        }
        return $total->getValue();
    }

    /**
     * Initialize quote totals array
     *
     * @return $this
     */
    public function _initTotals()
    {
        $this->totals = [];
        $this->totals['subtotal'] = $this->dataObjectFactory->create(
            [
            'code' => 'subtotal',
            'value' => $this->getSource()->getSubtotal(),
            'base_value' => $this->getSource()->getBaseSubtotal(),
            'label' => __('Subtotal')
            ]
        );
        
        $this->totals['grand_total'] = $this->dataObjectFactory->create(
            [
            'code' => 'grand_total',
            'strong' => true,
            'value' => $this->getSource()->getGrandTotal(),
            'base_value' => $this->getSource()->getBaseGrandTotal(),
            'label' => __('Grand Total'),
            'area' => 'footer'
            ]
        );
        
        if ($this->getSource()->getProposedPrice()) {
            $this->totals['proposed_price'] = $this->dataObjectFactory->create(
                [
                'code' => 'proposed_price',
                'strong' => true,
                'value' => $this->getSource()->getProposedPrice(),
                'base_value' => $this->getSource()->getProposedPrice(),
                'label' => __('Proposed Price'),
                'area' => 'footer'
                ]
            );
        }
        
        return $this;
    }
}
