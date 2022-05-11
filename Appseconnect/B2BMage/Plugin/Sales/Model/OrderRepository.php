<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Plugin\Sales\Model;

/**
 * Class History
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderRepository
{
    /**
     * OrderExtensionFactory
     *
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * OrderRepository constructor.
     *
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory OrderExtensionFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * AfterGet
     *
     * @param \Magento\Sales\Model\OrderRepository $subject Subject
     * @param $order   Order
     *
     * @return mixed
     */
    public function afterGet(\Magento\Sales\Model\OrderRepository $subject, $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }
        $extensionAttributes->setContactPersonId($order->getContactPersonId());
        $extensionAttributes->setIsPlacedbySalesrep($order->getIsPlacedbySalesrep());
        $extensionAttributes->setSalesrepId($order->getSalesrepId());
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    /**
     * GetOrderExtensionDependency
     *
     * @return \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    public function getOrderExtensionDependency()
    {
        $stockItemExtension = $this->orderExtensionFactory->create();
        return $stockItemExtension;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Sales\Model\OrderRepository $subject   Subject
     * @param $orderList OrderList
     *
     * @return mixed
     */
    public function afterGetList(\Magento\Sales\Model\OrderRepository $subject, $orderList)
    {
        $orderItems = $orderList->getItems();
        foreach ($orderItems as $orderListVal) {
            $extensionAttributes = $orderListVal->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->getOrderExtensionDependency();
            }
            $extensionAttributes->setContactPersonId($orderListVal->getContactPersonId());
            $extensionAttributes->setIsPlacedbySalesrep($orderListVal->getIsPlacedbySalesrep());
            $extensionAttributes->setSalesrepId($orderListVal->getSalesrepId());
            $orderListVal->setExtensionAttributes($extensionAttributes);
        }
        return $orderList;
    }
}
