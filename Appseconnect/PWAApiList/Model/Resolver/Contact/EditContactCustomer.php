<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Contact;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Action\Context;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Edit customer account resolver
 */
class EditContactCustomer implements ResolverInterface
{
    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Edit Customer constructor.
     *
     * @param Context $context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Appseconnect\B2BMage\Model\ContactFactory $contactFactory
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->contactFactory = $contactFactory;
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
        $customerId = $context->getUserId();
        $currentContactPerson = $this->customerFactory->create()->load($customerId);
        $checkCustomer = $this->helperContactPerson->isAdministrator($customerId);
        $filterCustomerByEmail = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect("entity_id")
            ->addAttributeToFilter("email", $args['input']['email'])
            ->getFirstItem();
        $filterCustomerByEmailEntityId = $filterCustomerByEmail->getData('entity_id');

        if ($checkCustomer == 1 && $currentContactPerson->getCustomerStatus() == 1) {

            if (empty($args['input']) || !is_array($args['input'])) {
                throw new GraphQlInputException(__('"input" value should be specified'));
            }

            if (isset($args['input']['contactperson_id'])) {
                $checkContactPersonData = $this->customerFactory->create()->load($args['input']['contactperson_id'])->getData();
                if ($checkContactPersonData) {
                    $args['input']['contactperson_id'] = $args['input']['contactperson_id'];
                } else {
                    throw new GraphQlInputException(__("Contact Person ID doesn't exist"));
                }
            }

            if (isset($args['input']['status'])) {
                if ($args['input']['status'] == 0 || $args['input']['status'] == 1) {
                    $args['input']['status'] = $args['input']['status'];
                } else {
                    throw new GraphQlInputException(__('"status" value should be 0 or 1'));
                }
            }

            if (isset($args['input']['role'])) {
                if ($args['input']['role'] == 1 || $args['input']['role'] == 2) {
                    $args['input']['role'] = $args['input']['role'];
                } else {
                    throw new GraphQlInputException(__('"role" value should be 1 or 2'));
                }
            }

            if (isset($args['input']['firstname'])) {
                $args['input']['firstname'] = $args['input']['firstname'];
            }

            if (isset($args['input']['lastname'])) {
                $args['input']['lastname'] = $args['input']['lastname'];
            }

            if (isset($args['input']['email'])) {
                if (isset($filterCustomerByEmailEntityId) && $filterCustomerByEmailEntityId != $args['input']['contactperson_id']) {
                    throw new GraphQlInputException(__('Email already exists.'));
                } else {
                    if (!\Zend_Validate::is(trim($args['input']['email']), 'EmailAddress')) {
                        throw new GraphQlInputException(__('Invalid Email Address'));
                    } else {
                        $args['input']['email'] = $args['input']['email'];
                    }
                }
            }

            $originalRequestData = $args['input'];

            $customer = $this->customerRepository->getById($originalRequestData['contactperson_id']);

            $customer->setCustomAttribute('contactperson_role', $originalRequestData['role']);
            $customer->setCustomAttribute('customer_status', $originalRequestData['status']);
            $customer->setFirstname($originalRequestData['firstname']);
            $customer->setLastname($originalRequestData['lastname']);
            if (isset($originalRequestData['email']) && $originalRequestData['email']) {
                $customer->setEmail($originalRequestData['email']);
            }

            $this->customerRepository->save($customer);

            $contactPersonId = $customer->getId();

            $contactPersonModel = $this->contactFactory->create()
                ->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('contactperson_id', $contactPersonId)
                ->getFirstItem();

            $contactPersonModel->setIsActive($originalRequestData['status']);
            $contactPersonModel->save();

            $customerData = $this->helperContactPerson->getCustomerId($context->getUserId());
            $customerId = $customerData['customer_id'];
            // contact person work
            $contactPersonData = [];
            $contactPersonData['customer_id'] = $customerId;
            $contactPersonData['contactperson_id'] = $contactPersonId;
            $contactPersonData['is_active'] = $originalRequestData['status'];
            $contactPersonData['firstname'] = $customer->getFirstname();
            $contactPersonData['lastname'] = $customer->getLastname();
            $contactPersonData['email'] = $customer->getEmail();

            return ['customer' => $contactPersonData];
        }
    }
}
