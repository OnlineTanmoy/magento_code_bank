<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


/**
 * Order sales field resolver, used for GraphQL request processing
 */
class CreateB2BCustomer implements ResolverInterface
{
    public $helperData;
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    public $accountManagement;
    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    public $addressDataFactory;

    public $addressRepository;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    public $dataObjectHelper;
    /**
     * @var \Magento\Customer\Api\Data\RegionInterfaceFactory
     */
    public $regionDataFactory;
    public $contactFactory;
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperData,
        AccountManagementInterface $accountManagement,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        AddressInterfaceFactory $addressDataFactory,
        AddressRepositoryInterface $addressRepository,
        DataObjectHelper $dataObjectHelper,
        RegionInterfaceFactory $regionDataFactory,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        ScopeConfigInterface  $scopeConfig

    ) {
        $this->helperData = $helperData;
        $this->accountManagement = $accountManagement;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerFactory = $customerFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->addressRepository = $addressRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->regionDataFactory = $regionDataFactory;
        $this->contactFactory = $contactFactory;
        $this->scopeConfig = $scopeConfig;

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
        try {
            if (!isset($args['input']['street2'])) {
                $args['input']['street2'] = '';
            }
            if (!isset($args['input']['street3'])) {
                $args['input']['street3'] = '';
            }
            $customer = $this->customerFactory->create();
            $customer->setEmail($args['input']['email']);
            $customer->setFirstname($args['input']['legalname']);
            $customer->setLastname(".");
            $customer->setCustomerType(4);
            $autoapproval = $this->scopeConfig->getValue(
                'insync_b2baccount/createb2b/enable_approval',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($autoapproval == 1) {
                $customer->setCustomerStatus(1);
            } else {
                $customer->setCustomerStatus(0);
            }

            $customer->save();
            $addressDataObject = $this->addressDataFactory->create();

            $addressDataObject->setFirstname($args['input']['legalname'])
                ->setLastname('.')
                ->setCountryId($args['input']['country_id'])
                ->setRegionId($args['input']['region_id'])
                ->setCity($args['input']['city'])
                ->setPostcode($args['input']['postcode'])
                ->setCustomerId($customer->getId())
                ->setStreet([$args['input']['street1'], $args['input']['street2'], $args['input']['street3']])
                ->setTelephone($args['input']['telephone'])
                ->setIsDefaultShipping(1)
                ->setIsDefaultBilling(1);

            $this->addressRepository->save($addressDataObject);

            $contactPerson = $this->customerDataFactory->create();
            $contactPerson->setFirstname($args['input']['contactfirstname']);
            $contactPerson->setLastname($args['input']['contactlastname']);
            $contactPerson->setEmail($args['input']['contactemail']);
            $contactPerson->setCustomAttribute('customer_type', 3);
            $contactPerson->setCustomAttribute('customer_status', 1);
            $contactPerson->setCustomAttribute('contactperson_role', 1);

            if ($args['input']['contactpassword'] == $args['input']['contactconfirmpassword']) {
                $contactPerson = $this->accountManagement->createAccount($contactPerson,
                    $args['input']['contactpassword']);
            } else {
                throw new GraphQlInputException(__('Please make sure your passwords match.'));
            }
            $this->saveContactPersonMapping($customer->getId(), $contactPerson->getId());
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('New B2B customer has not been created'));
        }
        return [
            'legalname' => $customer->getFirstname(),
            'email' => $customer->getEmail()
        ];
    }

    /**
     * @param array $originalRequestData
     * @param int $customerId
     * @param int $contactPersonId
     * @return void
     */
    private function saveContactPersonMapping($customerId, $contactPersonId)
    {
        $contactPersonData = [];
        $contactPersonData['customer_id'] = $customerId;
        $contactPersonData['contactperson_id'] = $contactPersonId;
        $contactPersonData['is_active'] = 0;
        $contactModel = $this->contactFactory->create();
        $contactModel->setData($contactPersonData);
        $contactModel->save();
    }
}
