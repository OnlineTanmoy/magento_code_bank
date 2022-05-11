<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Integration\Api\CustomerTokenServiceInterface;

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class GenerateCustomerToken extends \Magento\CustomerGraphQl\Model\Resolver\GenerateCustomerToken
{
    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @param CustomerTokenServiceInterface $customerTokenService
     */
    public function __construct(
        CustomerTokenServiceInterface                   $customerTokenService,
        \Magento\Customer\Model\CustomerFactory         $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper
    )
    {
        $this->customerTokenService = $customerTokenService;
        $this->customerFactory = $customerFactory;
        $this->contactHelper = $contactHelper;
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
        if (empty( $args['email'] )) {
            throw new GraphQlInputException( __( 'Specify the "email" value.' ) );
        }

        if (empty( $args['password'] )) {
            throw new GraphQlInputException( __( 'Specify the "password" value.' ) );
        }

        $filterCustomerByEmail = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect( "entity_id" )
            ->addAttributeToFilter( "email", $args['email'] )
            ->getFirstItem();

        if (!empty( $filterCustomerByEmail->getData() )) {
            $currentContactPersonId = $filterCustomerByEmail->getData( 'entity_id' );
            $currentContactPersonStatus = $this->customerFactory->create()
                ->load( $currentContactPersonId )
                ->getCustomerStatus();
            if ($this->contactHelper->isContactPerson( $filterCustomerByEmail )) {
                $customerId = $this->contactHelper->getContactCustomerId( $currentContactPersonId );
                $customerStatus = $this->customerFactory->create()
                    ->load( $customerId )
                    ->getCustomerStatus();
            } else {
                $customerStatus = null;
            }
            if ($currentContactPersonStatus == 1 && ($customerStatus == null || $customerStatus == 1)) {
                $token = $this->customerTokenService->createCustomerAccessToken( $args['email'], $args['password'] );
                return ['token' => $token];
            } else {
                throw new GraphQlInputException( __( 'Customer is Inactive.' ) );
            }
        } else {
            throw new GraphQlInputException( __( "EmailID doesn't exist" ) );
        }
    }
}






