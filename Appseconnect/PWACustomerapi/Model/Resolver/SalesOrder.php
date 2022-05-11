<?php
declare(strict_types=1);

namespace Appseconnect\PWACustomerapi\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Sales Order field resolver, used for GraphQL request processing
 */
class SalesOrder implements ResolverInterface
{
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterface $_orderdata
    ) {
        $this->orderdata = $_orderdata;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $salesId = $this->getSalesId($args);
        $salesData = $this->getSalesData($salesId);

        return $salesData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getSalesId(array $args): int
    {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"sales id should be specified'));
        }

        return (int)$args['id'];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getSalesData(int $orderId): array
    {
        try {
            $orderdata = $this->orderdata->loadByIncrementId($orderId);
            $orderId = $orderdata->getId();
            $order = $this->orderRepository->get($orderId);
            $billigAddress = $order->getBillingAddress()->getData();
            $shippingAddress = $order->getShippingAddress()->getData();
            foreach ($order->getAllVisibleItems() as $_item) {
                $itemsData[] = $_item->getData();
            }
            $pageData = [
                'increment_id' => $order->getIncrementId(),
                'grand_total' => $order->getGrandTotal(),
                'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                'created_at' => $order->getCreatedAt(),
                'status' => $order->getStatus(),
                'is_guest_customer' => !empty($order->getCustomerIsGuest()) ? 1 : 0,
                'shipping_method' => $order->getShippingMethod(),
                'shipping_address' => $shippingAddress,
                'billing_address' => $billigAddress,
                'items' => $itemsData
            ];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $pageData;
    }
}