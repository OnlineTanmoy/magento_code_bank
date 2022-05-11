<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


class CreditData implements ResolverInterface
{

    protected $storeManager;
    protected $helperData;
    public $_customerRepositoryInterface;

    // You can inject relevant classes in this constructor function.
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface        $storeManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data   $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {


        if (!isset( $value['model'] )) {
            throw new LocalizedException( __( '"model" value should be specified' ) );
        }
        $customer = $value['model'];
        $customerId = (int)$customer->getId();
        $collection = $this->_customerRepositoryInterface->getById( $customerId );
        // Get the custom attribute info of the customer.
        $output = '';
        if ($customer->getCustomAttribute( 'customer_type' )->getValue() == 3) {
            $parentid = $this->helperData->getCustomerId( $customerId );
            $companyId = $parentid['customer_id'];
            $parentData = $this->_customerRepositoryInterface->getById( $companyId );
            $parentinfo = $parentData->getCustomAttribute( 'customer_available_balance' )->getValue();
            $creditLimit = $parentData->getCustomAttribute( 'customer_credit_limit' )->getValue();

            if ($parentinfo == '') {
                $output = __( 'No Credit Limit' );
            } elseif ($parentinfo == 0.00) {

                if ($creditLimit == 0.00) {
                    $output = __( 'No Credit Limit Assigned' );
                } else {
                    $output = __( 'No Credit Limit, Clear Pending Payments' );
                }
            } else {
                $output = $parentinfo;
            }
        } else {
            $output = $customer->getCustomAttribute( 'customer_type' )->getValue();
        }
        return $output;
    }
}
