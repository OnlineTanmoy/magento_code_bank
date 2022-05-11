<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Sales\Invoice\Total;

/**
 * Class Discount
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Discount extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{

    /**
     * Collect
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice invoice
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $discountPercent = 0.00;
        $discountAmount = 0.00;
        $subtotal = $invoice->getSubtotal();
        if ($discountPercent = $invoice->getOrder()->getCustomerDiscount()) {
            $discountAmount = $subtotal * ( $discountPercent / 100);
        }
        $invoice->setGrandTotal($invoice->getGrandTotal() - $discountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $discountAmount);
        return $this;
    }
}
