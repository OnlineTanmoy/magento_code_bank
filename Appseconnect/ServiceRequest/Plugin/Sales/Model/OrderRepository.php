<?php

namespace Appseconnect\ServiceRequest\Plugin\Sales\Model;

class OrderRepository
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;

    /**
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    public $serviceRequestFactory;

    /**
     * OrderRepository constructor.
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Appseconnect\ServiceRequest\Model\RequestPostFactory $serviceRequestFactory
    )
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderFactory = $orderFactory;
        $this->serviceRequestFactory = $serviceRequestFactory;
    }

    /**
     * load entity
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Model\OrderRepository $subject ,
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(\Magento\Sales\Model\OrderRepository $subject, $order)
    {
        if ($order->getServiceId()) {
            $extensionAttributes = $order->getExtensionAttributes();
            if (!$extensionAttributes) $extensionAttributes = $this->getOrderExtensionDependency();
            $extensionAttributes->setServiceId($order->getServiceId());
            $extensionAttributes->setServiceNumber($order->getServiceNumber());
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $order;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private function getOrderExtensionDependency()
    {
        $stockItemExtension = $this->orderExtensionFactory->create();

        return $stockItemExtension;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $orderList
     * @param \Magento\Sales\Model\OrderRepository $subject
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(\Magento\Sales\Model\OrderRepository $subject, $orderList)
    {
        $orderItems = $orderList->getItems();
        foreach ($orderItems as $orderListVal) {
            $extensionAttributes = $orderListVal->getExtensionAttributes();
            if (!$extensionAttributes) $extensionAttributes = $this->getOrderExtensionDependency();
            $extensionAttributes->setServiceId($orderListVal->getServiceId());
            $extensionAttributes->setServiceNumber($orderListVal->getServiceNumber());
            $orderListVal->setExtensionAttributes($extensionAttributes);
        }

        return $orderList;
    }

    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    )
    {

        $extensionAttributes = $resultOrder->getExtensionAttributes();
        if (null !== $extensionAttributes) {
            //$extensionAttributes->getServiceId()->getValue();
            if ($resultOrder->getEntityId() && $extensionAttributes->getServiceId()) {
                $order = $this->orderFactory->create()->load($resultOrder->getEntityId());
                $order->setServiceId($extensionAttributes->getServiceId())
                    ->setServiceNumber($extensionAttributes->getServiceNumber())->save();

                $service = $this->serviceRequestFactory->create()->load($extensionAttributes->getServiceId());
                $service->setIsDraft(0);
                $service->setOrderId($order->getId())->save();
            }
        }

        return $resultOrder;
    }


}
