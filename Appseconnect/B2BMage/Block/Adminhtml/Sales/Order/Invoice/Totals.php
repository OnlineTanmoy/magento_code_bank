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

namespace Appseconnect\B2BMage\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;

/**
 * Class Totals
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{

    /**
     * Order invoice
     *
     * @var Invoice|null
     */
    public $invoice = null;

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
     * @param \Magento\Framework\Registry                      $registry          Registry
     * @param \Magento\Sales\Helper\Admin                      $adminHelper       AdminHelper
     * @param \Magento\Framework\DataObject\Factory            $dataObjectFactory DataObjectFactory
     * @param array                                            $data              Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get invoice
     *
     * @return Invoice|null
     */
    public function getInvoice()
    {
        if ($this->invoice === null) {
            if ($this->hasData('invoice')) {
                $this->invoice = $this->_getData('invoice');
            } elseif ($this->_coreRegistry->registry('currentinvoice')) {
                $this->invoice = $this->_coreRegistry->registry('currentinvoice');
            } elseif ($this->getParentBlock()->getInvoice()) {
                $this->invoice = $this->getParentBlock()->getInvoice();
            }
        }
        return $this->invoice;
    }

    /**
     * Get source
     *
     * @return Invoice|null
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    public function _initTotals()
    {
        parent::_initTotals();
        if ($this->getSource()->getOrder()->getCustomerDiscount() > 0) {
            $this->_totals['customer_discount'] = $this->dataObjectFactory->create(
                [
                    'code' => 'customer_discount',
                    'value' => $this->getDiscount($this->_totals['subtotal']->getValue(), $this->getSource()->getOrder()->getCustomerDiscount()),
                    'base_value' => $this->getDiscount($this->_totals['subtotal']->getBaseValue(), $this->getSource()->getOrder()->getCustomerDiscount()),
                    'label' => 'Customer Discount( ' . $this->getSource()->getOrder()->getCustomerDiscount() . '% )',
                ]
            );
        }
        return $this;
    }

    /**
     * GetDiscount
     *
     * @param $amount          Amount
     * @param $discountPercent DiscountPercent
     *
     * @return float|int
     */
    public function getDiscount($amount, $discountPercent)
    {
        return $amount * ($discountPercent / 100);
    }
}
