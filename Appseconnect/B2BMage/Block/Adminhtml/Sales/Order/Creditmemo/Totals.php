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

namespace Appseconnect\B2BMage\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class Totals
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /**
     * Creditmemo
     *
     * @var Creditmemo|null
     */
    public $_creditmemo;

    /**
     * Initialize creditmemo totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'adjustment_positive',
                    'value' => $this->getSource()->getAdjustmentPositive(),
                    'base_value' => $this->getSource()->getBaseAdjustmentPositive(),
                    'label' => __('Adjustment Refund'),
                ]
            )
        );
        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'adjustment_negative',
                    'value' => $this->getSource()->getAdjustmentNegative(),
                    'base_value' => $this->getSource()->getBaseAdjustmentNegative(),
                    'label' => __('Adjustment Fee'),
                ]
            )
        );
        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'customer_discount',
                    'value' => $this->getDiscount($this->_totals['subtotal']->getValue(), $this->getSource()->getOrder()->getCustomerDiscount()),
                    'base_value' => $this->getDiscount($this->_totals['subtotal']->getBaseValue(), $this->getSource()->getOrder()->getCustomerDiscount()),
                    'label' => 'Customer Discount(  ' . $this->getSource()->getOrder()->getCustomerDiscount() . '% )',
                ]
            )
        );
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
