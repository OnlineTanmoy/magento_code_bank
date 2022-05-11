<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AdditionaData implements ResolverInterface
{

    protected $storeManager;
    protected $helperData;
    protected $_customerRepositoryInterface;

    // You can inject relevant classes in this constructor function.
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $output = [];
        $customer = $value['model'];
        $customerId = (int)$customer->getId();

        // Get the custom attribute info of the customer.
        $output = $this->getCustomerAdditionaInfo($customerId);
        return $output;
    }

    private function getCustomerAdditionaInfo($customerId)
    {
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        return $this->helperData->getParentCustomerName($customer);
    }
}