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
namespace Appseconnect\B2BMage\Block\CustomerSpecificDiscount\Sales\Order;

/**
 * Interface CustomerDiscount
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerDiscount extends \Magento\Framework\View\Element\Template
{

    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    public $config;

    /**
     * Order
     *
     * @var Order
     */
    public $order;

    /**
     * Data object
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    public $dataObjectFactory;

    /**
     * Source
     *
     * @var \Magento\Framework\DataObject
     */
    public $source;

    /**
     * CustomerDiscount constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context           context
     * @param \Magento\Framework\DataObject\Factory            $dataObjectFactory data object
     * @param \Magento\Tax\Model\Config                        $taxConfig         tax
     * @param array                                            $data              data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->config = $taxConfig;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $data);
    }

    /**
     * Check if we need display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get store
     *
     * @return $this
     */
    public function getStore()
    {
        return $this->order->getStore();
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get label property
     *
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get value properties
     *
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();
        
        $store = $this->getStore();
        if ($this->source->getCustomerDiscount() > 0) {
            $amount = $this->dataObjectFactory->create(
                [
                'code' => 'customer_discount',
                'strong' => false,
                'value' => $this->source->getCustomerDiscountAmount(),
                'label' => __('Customer Discount( ' . $this->source->getCustomerDiscount() . '% )')
                ]
            );
            
            $parent->addTotal($amount, 'customer_discount');
            $parent->addTotal($amount, 'customer_discount');
        }
        
        return $this;
    }

}
