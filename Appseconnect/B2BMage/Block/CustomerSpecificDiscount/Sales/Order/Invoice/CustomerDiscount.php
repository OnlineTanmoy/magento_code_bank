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
namespace Appseconnect\B2BMage\Block\CustomerSpecificDiscount\Sales\Order\Invoice;

use Magento\Sales\Model\Order;

/**
 * Interface CustomerDiscount
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerDiscount extends \Magento\Sales\Block\Order\Totals
{

    /**
     * Data object
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    private $_dataObjectFactory;

    /**
     * CustomerDiscount constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            context
     * @param \Magento\Framework\Registry                      $registry           registry
     * @param \Magento\Framework\DataObject\Factory            $_dataObjectFactory data object
     * @param array                                            $data               data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DataObject\Factory $_dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->_isScopePrivate = true;
        $this->_dataObjectFactory = $_dataObjectFactory;
    }

    /**
     * Invoice
     *
     * @var Order|null
     */
    protected $invoice = null;

    /**
     * Get Invoice
     *
     * @return Order
     */
    public function getInvoice()
    {
        $currentInvoice = $this->_coreRegistry->registry('current_invoice');
        if ($this->invoice === null) {
            if ($this->hasData('invoice')) {
                $this->invoice = $this->_getData('invoice');
            } elseif ($currentInvoice) {
                $this->invoice = $currentInvoice;
            } elseif ($this->getParentBlock()->getInvoice()) {
                $this->invoice = $this->getParentBlock()->getInvoice();
            }
        }
        return $this->invoice;
    }

    /**
     * Set Invoice
     *
     * @param Order $invoiceData invoice data
     *
     * @return $this
     */
    public function setInvoice($invoiceData)
    {
        $this->invoice = $invoiceData;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        $invoice = $this->getInvoice();
        return $invoice;
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        $baseGrandTotalKey = 'base_grandtotal';
        parent::_initTotals();
        $this->removeTotal($baseGrandTotalKey);
        $this->removeTotal('grand_total');
        if ($this->getSource()->getOrder()->getCustomerDiscount() > 0) {
            $this->_totals['customer_discount'] = $this->_dataObjectFactory->create(
                [
                    'code' => 'customer_discount',
                    'value' => $this->getSource()->getSubtotal() * ( $this->getSource()->getOrder()->getCustomerDiscount() / 100 ),
                    'base_value' => $this->getSource()->getBaseSubtotal() * ( $this->getSource()->getOrder()->getCustomerDiscount() / 100 ),
                    'label' => 'Customer Discount( '.$this->getSource()->getOrder()->getCustomerDiscount().'%)',
                ]
            );
        }
        $this->_totals['grand_total'] = $this->_dataObjectFactory->create(
            [
                'code' => 'grand_total',
                'field' => 'grand_total',
                'strong' => true,
                'value' => $this->getSource()->getGrandTotal(),
                'label' => __('Grand Total'),
            ]
        );

        return $this;
    }

}
