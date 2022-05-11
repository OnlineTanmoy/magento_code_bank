<?php
namespace Appseconnect\ServiceRequest\Model\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DataObject;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{

    public function send(Order $order, $forceSyncMode = false)
    {
        $orderItems = $order->getAllItems();
        $serviceSku = false;
        foreach ($orderItems as $item) {
            if ($item->getSku() == 'service') {
                $serviceSku = true;
            }
        }
        if($serviceSku) {
            return false;
        }else{
            return parent::send($order, $forceSyncMode);
        }
    }
}
