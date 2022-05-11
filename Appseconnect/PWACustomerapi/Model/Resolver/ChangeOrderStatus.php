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
class ChangeOrderStatus implements ResolverInterface
{
    protected $logger;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterface $_orderdata,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->orderdata = $_orderdata;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {
        //$salesId = $this->getSalesId($args);
        //$this->logger->info(json_encode($args['input']['id']));

        $orderId = $args['input']['id'];
        $order_status = $args['input']['status'];
        $orderdata = $this->orderdata->loadByIncrementId($orderId);
        $orderId = $orderdata->getId();
        $order = $this->orderRepository->get($orderId);
        $order->setStatus($order_status);
        $order->setState($order_status);
        $order->save();

        $updated_order = $this->orderRepository->get($orderId);
        
        return [
          'status' =>  $updated_order->getStatus(),
          'updated_at' => $updated_order->getUpdatedAt()
        ];
    }
}