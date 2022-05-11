<?php

namespace Appseconnect\AvailableToPromise\Observer;

use Magento\Framework\Event\ObserverInterface;

class EmailSetVariable implements ObserverInterface
{
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transport = $observer->getTransport();
        if ($transport->getOrder()->getDeliveryInfo()) {
            $transport['shipping_msg'] = 'Delivery Date: ' . date('F j, Y',
                    strtotime($transport->getOrder()->getDeliveryInfo()));
        }

    }
}