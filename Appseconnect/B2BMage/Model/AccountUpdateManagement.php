<?php
/**
 * Namespace
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Customer\Model\Customer\NotificationStorage;

/**
 * Class AccountManagement
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AccountUpdateManagement
{
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * ContactFactory
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;
    
    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $eventManager;
    
    /**
     * ImageProcessorInterface
     *
     * @var ImageProcessorInterface
     */
    public $imageProcessor;
    
    /**
     * ExtensibleDataObjectConverter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $storeManager;
    
    /**
     * CustomerRegistry
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    public $customerRegistry;
    
    /**
     * CustomerRepository
     *
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    public $customerRepository;

    /**
     * AccountUpdateManagement constructor.
     *
     * @param CustomerFactory                                          $customerFactory               CustomerFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository            CustomerRepository
     * @param ContactFactory                                           $contactFactory                ContactFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data          $helperContactPerson           HelperContactPerson
     * @param CustomerRegistry                                         $customerRegistry              CustomerRegistry
     * @param \Magento\Framework\Event\ManagerInterface                $eventManager                  EventManager
     * @param \Magento\Store\Model\StoreManagerInterface               $storeManager                  StoreManager
     * @param ExtensibleDataObjectConverter                            $extensibleDataObjectConverter ExtensibleDataObjectConverter
     * @param ImageProcessorInterface                                  $imageProcessor                ImageProcessor
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->eventManager = $eventManager;
        $this->contactFactory = $contactFactory;
        $this->imageProcessor = $imageProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Save
     *
     * @param CustomerInterface $contactPerson ContactPerson
     * @param null              $passwordHash  PasswordHash
     *
     * @return mixed
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $contactPerson, $passwordHash = null)
    {
        $prevCustomerData = null;
        $this->setContactCustomAttribute($contactPerson);
        if ($contactPerson->getId()) {
            $prevCustomerData = $this->customerRepository->getById($contactPerson->getId());
        }
        $contactPerson = $this->imageProcessor
            ->save($contactPerson, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $prevCustomerData);
        if ($contactPerson->getId()) {
            $customerId = $this->helperContactPerson->getCustomerId($contactPerson->getId());
            $customerDetail = $this->helperContactPerson->checkCustomerStatus($customerId['customer_id'], true);
            $this->setContactPersonData($contactPerson, $customerDetail);
        }
        $origAddresses = $contactPerson->getAddresses();
        $contactPerson->setAddresses([]);
        $customerData = $this
            ->extensibleDataObjectConverter
            ->toNestedArray($contactPerson, [], '\Magento\Customer\Api\Data\CustomerInterface');
        $contactPerson->setAddresses($origAddresses);
        $customerModel = $this->customerFactory->create(
            [
            'data' => $customerData
            ]
        );
        $storeId = $customerModel->getStoreId();
        if ($storeId === null) {
            $customerModel->setStoreId(
                $this->storeManager->getStore()
                    ->getId()
            );
        }
        $customerModel->setId($contactPerson->getId());
        // Need to use attribute set or future updates can cause data loss
        if (! $customerModel->getAttributeSetId()) {
            $customerModel
            ->setAttributeSetId(\Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        }
        // Populate model with secure data
        if ($contactPerson->getId()) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($contactPerson->getId());
            $this->setContactAsCustomer($customerModel, $customerSecure);
        } else {
            if ($passwordHash) {
                $customerModel->setPasswordHash($passwordHash);
            }
        }
        // If customer email was changed, reset RpToken info
        if ($prevCustomerData && $prevCustomerData->getEmail() !== $customerModel->getEmail()) {
            $customerModel->setRpToken(null);
            $customerModel->setRpTokenCreatedAt(null);
        }
        $customerModel->save();
        $this->customerRegistry->push($customerModel);
        $customerId = $customerModel->getId();
        if ($contactPerson->getAddresses() !== null) {
            $this->deleteContactAddress($contactPerson, $customerId);
        }
        $savedCustomer = $this->customerRepository->get($contactPerson->getEmail(), $contactPerson->getWebsiteId());

        $this->processContactPerson($savedCustomer);
        return $savedCustomer;
    }
    
    /**
     * SetContactCustomAttribute
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPerson ContactPerson
     *
     * @return void
     */
    public function setContactCustomAttribute($contactPerson)
    {
        if ($contactPerson->getCustomAttribute('contactperson_role')) {
            $this->setContactPersonRole($contactPerson);
        }
        $contactPerson->setCustomAttribute('customer_type', 3);
        $contactPerson->setCustomAttribute('ins_customer_type', 3);
        if ($contactPerson->getCustomAttribute('customer_status')) {
            $contactPerson
                ->setCustomAttribute(
                    'customer_status', $contactPerson->getCustomAttribute('customer_status')
                        ->getValue()
                );
        }
    }
    
    /**
     * SetContactPersonRole
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPerson ContactPerson
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return void
     */
    public function setContactPersonRole($contactPerson)
    {
        if ($contactPerson->getCustomAttribute('contactperson_role')->getValue() == '') {
            throw new \Magento\Framework\Exception\InputException(
                __("Please specify value for [contactperson_role]")
            );
        }
        $contactPersonRole = $contactPerson->getCustomAttribute('contactperson_role')->getValue();
        if ($contactPersonRole != 1 && $contactPersonRole != 2) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Invalid value [contactperson_role]", $contactPersonRole)
            );
        }
        if ($contactPerson->getCustomAttribute('contactperson_role')->getValue() != '') {
            $contactPerson
                ->setCustomAttribute(
                    'contactperson_role', $contactPerson->getCustomAttribute('contactperson_role')
                        ->getValue()
                );
        }
    }
    
    /**
     * DeleteContactAddress
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPerson ContactPerson
     * @param int                                          $customerId    CustomerId
     *
     * @return void
     */
    public function deleteContactAddress($contactPerson, $customerId)
    {
        $existingAddressIds = $this->getExistingContactAddress($contactPerson);
        $savedAddressIds = [];
        $savedAddressIds = $this->getSavedAddressIds($contactPerson, $customerId);
        $addressIdsToDelete = array_diff($existingAddressIds, $savedAddressIds);
        foreach ($addressIdsToDelete as $addressId) {
            $this->addressRepository->deleteById($addressId);
        }
    }
    
    /**
     * GetExistingContactAddress
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPerson ContactPerson
     *
     * @return array
     */
    public function getExistingContactAddress($contactPerson)
    {
        if ($contactPerson->getId()) {
            $existingAddresses = $this->getById($contactPerson->getId())
                ->getAddresses();
            $getIdFunc = function ($address) {
                return $address->getId();
            };
            $existingAddressIds = array_map($getIdFunc, $existingAddresses);
        } else {
            $existingAddressIds = [];
        }
        
        return $existingAddressIds;
    }
    
    /**
     * SetContactPersonData
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPerson  ContactPerson
     * @param array                                        $customerDetail CustomerDetail
     *
     * @return void
     */
    public function setContactPersonData($contactPerson, $customerDetail)
    {
        $contactPerson->setWebsiteId($customerDetail['website_id']);
        $contactPerson->setStoreId($customerDetail['store_id']);
        $contactPerson->setGroupId($customerDetail['group_id']);
    }
    
    /**
     * SetContactAsCustomer
     *
     * @param \Magento\Customer\Model\CustomerFactory            $customerModel  CustomerModel
     * @param \Magento\Customer\Model\Data\CustomerSecureFactory $customerSecure CustomerSecure
     *
     * @return void
     */
    public function setContactAsCustomer($customerModel, $customerSecure)
    {
        $customerModel->setRpToken($customerSecure->getRpToken());
        $customerModel->setRpTokenCreatedAt($customerSecure->getRpTokenCreatedAt());
        $customerModel->setPasswordHash($customerSecure->getPasswordHash());
        $customerModel->setFailuresNum($customerSecure->getFailuresNum());
        $customerModel->setFirstFailure($customerSecure->getFirstFailure());
        $customerModel->setLockExpires($customerSecure->getLockExpires());
    }
    
    /**
     * GetSavedAddressIds
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $contactPersonObj ContactPersonObj
     * @param int                                          $customerId       CustomerId
     *
     * @return array
     */
    public function getSavedAddressIds($contactPersonObj, $customerId)
    {
        $savedAddressIdsData = [];
        foreach ($contactPersonObj->getAddresses() as $address) {
            $address->setCustomerId($customerId)->setRegion($address->getRegion());
            $this->saveContactAddress($address);
            if ($address->getId()) {
                $savedAddressIdsData[] = $address->getId();
            }
        }
        return $savedAddressIdsData;
    }
    
    /**
     * SaveContactAddress
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address Address
     *
     * @return void
     */
    public function saveContactAddress($address)
    {
        $this->addressRepository->save($address);
    }

    /**
     * ProcessContactPerson
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $savedCustomer SavedCustomer
     *
     * @return void
     */
    public function processContactPerson($savedCustomer)
    {
        $contactPersonId = $savedCustomer->getId();
        $contactPersonData = [];
        $contactPersonData['is_active'] = $savedCustomer->getCustomAttribute('customer_status')->getValue();
        $contactModel = $this->contactFactory->create();
        $contactCollection = $contactModel
            ->getCollection()
            ->addFieldToFilter('contactperson_id', $contactPersonId)
            ->load();
        if ($contactCollection->getSize()) {
            $contactCollectionData = $contactCollection->getData();
            $contactPersonCollectionData = $contactCollectionData[0];
            if (isset($contactPersonCollectionData)) {
                $contactPersonData['id'] = $contactPersonCollectionData['id'];
                $contactModel->addData($contactPersonData);
                $contactModel->save();
            }
        }
    }
}
