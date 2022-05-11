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

namespace Appseconnect\B2BMage\Model\Sales\Order\Creditmemo\Totals;

/**
 * Class Discount
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Discount extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setDiscountAmount(0);
        $creditmemo->setBaseDiscountAmount(0);

        $order = $creditmemo->getOrder();

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;

        $baseShippingAmount = (float)$creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseShippingDiscount = $baseShippingAmount *
                $order->getBaseShippingDiscountAmount() /
                $order->getBaseShippingAmount();
            $shippingDiscount = $order->getShippingAmount() * $baseShippingDiscount / $order->getBaseShippingAmount();
            $totalDiscountAmount = $totalDiscountAmount + $shippingDiscount;
            $baseTotalDiscountAmount = $baseTotalDiscountAmount + $baseShippingDiscount;
        }


        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();

            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemDiscount = (double)$orderItem->getDiscountInvoiced();
            $baseOrderItemDiscount = (double)$orderItem->getBaseDiscountInvoiced();
            $orderItemQty = $orderItem->getQtyInvoiced();

            if ($orderItemDiscount && $orderItemQty) {
                $discount = $orderItemDiscount - $orderItem->getDiscountRefunded();
                $baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseDiscountRefunded();
                if (!$item->isLast()) {
                    $availableQty = $orderItemQty - $orderItem->getQtyRefunded();
                    $discount = $creditmemo->roundPrice($discount / $availableQty * $item->getQty(), 'regular', true);
                    $baseDiscount = $creditmemo->roundPrice(
                        $baseDiscount / $availableQty * $item->getQty(),
                        'base',
                        true
                    );
                }

                $item->setDiscountAmount($discount);
                $item->setBaseDiscountAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount += $baseDiscount;
            }
        }

        $creditmemo->setDiscountAmount(-$totalDiscountAmount);
        $creditmemo->setBaseDiscountAmount(-$baseTotalDiscountAmount);

        $discountPercent = $order->getCustomerDiscount();
        if ($discountPercent) {
            $discountAmount = $creditmemo->getGrandTotal() * ($discountPercent / 100);
            $totalDiscountAmount += $discountAmount;
            $discountAmount = $creditmemo->getBaseGrandTotal() * ($discountPercent / 100);
            $baseTotalDiscountAmount += $discountAmount;
        }

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        return $this;
    }
}
