<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Order sales field resolver, used for GraphQL request processing
 */
class CustomerData implements ResolverInterface
{
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        CollectionFactory $orderCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerCollection = $customerCollection;
        $this->addressFactory = $addressFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helperData = $helperData;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
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
        return $this->getSalesData($salesId);
    }

    /**
     * @param array $args
     * @return string
     * @throws GraphQlInputException
     */
    private function getSalesId(array $args): string
    {
        if (!isset($args['telephone'])) {
            throw new GraphQlInputException(__('"telephone number should be specified'));
        }

        return (string)$args['telephone'];
    }

    /**
     * @param string $telephone
     * @return string
     * @throws GraphQlNoSuchEntityException
     */
    private function getSalesData(string $telephone): array
    {
        try {

            $address = $this->addressFactory->create()
                ->getCollection()
                ->addFieldToFilter('telephone', $telephone)
                ->getFirstItem();

            $collection = $this->_customerRepositoryInterface->getById($address->getCustomerId());
            if ($collection->getCustomAttribute('customer_type')->getValue() == 3) {
                $parentid = $this->helperData->getCustomerId($address->getCustomerId());
                $customerId = $parentid['customer_id'];
            }
            else {
                $customerId = $address->getCustomerId();
            }
            $customerOrder = $this->orderCollectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId);

            $salesOrder = [];
            foreach($customerOrder as $item)
            {
                $orderId = $item->getId();
                $salesOrder['allOrderRecords'][$orderId]['increment_id'] = $item->getIncrementId();
                $salesOrder['allOrderRecords'][$orderId]['status'] = $item->getStatus();
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $salesOrder;
    }
}
