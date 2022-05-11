<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Contact;

use Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Create customer account resolver
 */
class CreateContactCustomer implements ResolverInterface
{
    /**
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    protected $_accountmanagement;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CreateCustomer constructor.
     *
     * @param \Appseconnect\B2BMage\Model\ContactFactory $contactFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param AccountManagementInterface $customerAccountManagement
     * @param StoreManagerInterface $storeManager
     *
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AccountManagement $accountmanagement,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager
    ) {
        $this->contactFactory = $contactFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->_accountmanagement = $accountmanagement;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
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

        $checkEmailIfExist = $this->emailExistOrNot($args['input']['email']);

        if ($checkCustomer == 1 && $currentContactPerson->getCustomerStatus() == 1) {
            if (empty($args['input']) || !is_array($args['input'])) {
                throw new GraphQlInputException(__('"input" value should be specified'));
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
                if ($checkEmailIfExist) {
                    if (!\Zend_Validate::is(trim($args['input']['email']), 'EmailAddress')) {
                        throw new GraphQlInputException(__('Invalid Email Address'));
                    } else {
                        $args['input']['email'] = $args['input']['email'];
                    }
                } else {
                    throw new GraphQlInputException(__('Email already exists.'));
                }
            }

            $originalRequestData = $args['input'];
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($currentContactPerson->getWebsiteId());
            $customer->setCustomerType(3);
            $customer->setContactpersonRole($originalRequestData['role']);
            $customer->setCustomerStatus($originalRequestData['status']);
            $customer->setFirstname($originalRequestData['firstname']);
            $customer->setLastname($originalRequestData['lastname']);
            $customer->setEmail($originalRequestData['email']);
            $customer->save();
            $this->_accountmanagement->initiatePasswordReset($customer->getEmail(), AccountManagement::EMAIL_RESET,
                $currentContactPerson->getWebsiteId());
            $contactPersonId = $customer->getId();
            $customerData = $this->helperContactPerson->getCustomerId($context->getUserId());
            $customerId = $customerData['customer_id'];

            // contact person work
            $contactPersonData = [];
            $contactPersonData['customer_id'] = $customerId;
            $contactPersonData['contactperson_id'] = $contactPersonId;
            $contactPersonData['is_active'] = $originalRequestData['status'];
            $contactModel = $this->contactFactory->create();
            $contactModel->setData($contactPersonData);
            $contactModel->save();
            $contactPersonData['firstname'] = $customer->getFirstname();
            $contactPersonData['lastname'] = $customer->getLastname();
            $contactPersonData['email'] = $customer->getEmail();
            return ['customer' => $contactPersonData];
        }
    }

    /**
     *
     * @return bool
     */
    public function emailExistOrNot($email): bool
    {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;
    }
}
