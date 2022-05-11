<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class EnableQuote implements ResolverInterface
{
    public $helperContactPerson;
    public $customerFactory;
    public $quotationHelper;
    public $customerRepository;

    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data   $helperContactPerson,
        \Appseconnect\B2BMage\Helper\Quotation\Data       $quotationHelper,
        \Magento\Customer\Model\CustomerFactory           $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->helperContactPerson = $helperContactPerson;
        $this->quotationHelper = $quotationHelper;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset( $value['model'] )) {
            throw new LocalizedException( __( '"model" value should be specified' ) );
        }
        $customer = $value['model'];

        $CurrentCustomer = $this->customerFactory->create()->load( (int)$customer->getId() );

        if ($this->helperContactPerson->isContactPerson( $CurrentCustomer )) {
            $ParentCustomerId = $this->helperContactPerson->getContactCustomerId( (int)$customer->getId() );
            $ParentCustomer = $this->customerRepository->getbyId( $ParentCustomerId );
            if ($ParentCustomer->getCustomAttribute( 'enable_quote' ) != null) {
                if ($ParentCustomer->getCustomAttribute( 'enable_quote' )->getValue() && $this->quotationHelper->isQuotationEnabled()) {
                    return '1';
                } else {
                    return '0';
                }
            }
        } else {
            return "0";
        }
    }
}
