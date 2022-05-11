<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\SalesRep;

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
class SalesrepContactPersonCreate implements ResolverInterface
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
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $salesrephelper;

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
        \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrephelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AccountManagement $accountmanagement,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager
    ) {
        $this->contactFactory = $contactFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->salesrephelper = $salesrephelper;
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
        if ($customerId) {
            if ($this->salesrephelper->isSalesrep($customerId)) {
                if ($this->emailExistOrNot($args['input']['email'])) {
                    $salesRepCustomer = $this->customerFactory->create()->load($customerId);
                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($salesRepCustomer->getWebsiteId());
                    $customer->setCustomerType(3);
                    $customer->setContactpersonRole(2);
                    $customer->setCustomerStatus(1);
                    $customer->setFirstname($args['input']['firstname']);
                    $customer->setLastname($args['input']['lastname']);
                    $customer->setEmail($args['input']['email']);
                    $customer->save();
                    $this->_accountmanagement->initiatePasswordReset($customer->getEmail(),
                        AccountManagement::EMAIL_RESET,
                        $salesRepCustomer->getWebsiteId());

                    // contact person
                    $contactPersonData = [] ;
                    $contactPersonData['customer_id'] = $args['input']['companyId'];
                    $contactPersonData['contactperson_id'] = $customer->getId();
                    $contactModel = $this->contactFactory->create();
                    $contactPersonData['is_active'] = 1;
                    $contactModel->setData($contactPersonData);
                    $contactModel->save();

                    return [
                        'firstname'=>$customer->getFirstname(),
                        'lastname'=>$customer->getLastname(),
                        'email'=>$customer->getEmail()
                    ];
                } else {
                    throw new GraphQlInputException(__('Email already exists.'));
                }
            } else {
                throw new GraphQlInputException(__('Access Denied'));
            }
        } else {
            throw new GraphQlInputException(__('Access Denied'));
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
