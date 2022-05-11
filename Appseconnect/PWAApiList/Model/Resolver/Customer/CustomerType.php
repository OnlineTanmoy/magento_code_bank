<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CustomerType implements ResolverInterface
{
    public $helperContactPerson;

    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    )
    {
        $this->helperContactPerson = $helperContactPerson;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $output = [];
        $customer = $value['model'];
        $customerId = (int)$customer->getId();
        $checkCustomer = $this->helperContactPerson->isAdministrator($customerId);

        // Get the custom attribute info of the customer.
        if ($customer->getCustomAttribute('customer_type')->getValue() == 3) {
            if ($checkCustomer == 1) {
                $output = "admin_contactperson";
            } else {
                $output = "standrad_contactperson";
            }
        } else {
            if ($customer->getCustomAttribute('customer_type')->getValue() == 1) {
                $output = "b2c_customer";
            }
            if ($customer->getCustomAttribute('customer_type')->getValue() == 2) {
                $output = "salesrep";
            }
        }
        return $output;
    }
}
