<?php

/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Model\ResourceModel;

use Appseconnect\B2BMage\Api\Salesrep\SalesrepRepositoryInterface;
use Appseconnect\B2BMage\Model\ResourceModel\SalesrepFactory as SalesrepResourceFactory;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer\NotificationStorage;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Class SalesrepRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SalesrepRepository implements SalesrepRepositoryInterface
{

    /**
     * AccountManagement
     *
     * @var \Magento\Customer\Model\AccountManagement
     */
    public $accountManager;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helperSalesrep;

    /**
     * SalesrepgridFactory
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepgridFactory
     */
    public $salesrepGridFactory;

    /**
     * SalesrepFactory
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesrepModelFactory;

    /**
     * CollectionFactory
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory
     */
    public $salesrepCollectionFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * SalesrepProcessor
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepProcessor
     */
    public $salesrepProcessor;

    /**
     * SalesrepResourceFactory
     *
     * @var SalesrepResourceFactory
     */
    public $salesrepResourceModelFactory;

    /**
     * CustomerSearchResultsInterfaceFactory
     *
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

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
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * CustomerRepository
     *
     * @var CustomerRepository
     */
    public $customerRepository;

    /**
     * CustomerRegistry
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    public $customerRegistry;

    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $eventManager;

    /**
     * SalesrepRepository constructor.
     *
     * @param CustomerRepository                                               $customerRepository            CustomerRepository
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory               CustomerFactory
     * @param \Appseconnect\B2BMage\Model\SalesrepProcessor                    $salesrepProcessor             SalesrepProcessor
     * @param \Magento\Customer\Model\AccountManagement                        $accountManager                AccountManager
     * @param \Appseconnect\B2BMage\Model\SalesrepgridFactory                  $salesrepGridFactory           SalesrepGridFactory
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data                       $helperSalesrep                HelperSalesrep
     * @param \Appseconnect\B2BMage\Model\SalesrepFactory                      $salesrepModelFactory          SalesrepModelFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                  $helperContactPerson           HelperContactPerson
     * @param Salesrep\CollectionFactory                                       $salesrepCollectionFactory     SalesrepCollectionFactory
     * @param \Magento\Customer\Model\CustomerRegistry                         $customerRegistry              CustomerRegistry
     * @param \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory          SearchResultsFactory
     * @param \Magento\Framework\Event\ManagerInterface                        $eventManager                  EventManager
     * @param \Magento\Store\Model\StoreManagerInterface                       $storeManager                  StoreManager
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter             $extensibleDataObjectConverter ExtensibleDataObjectConverter
     * @param ImageProcessorInterface                                          $imageProcessor                ImageProcessor
     * @param SalesrepFactory                                                  $salesrepResourceModelFactory  SalesrepResourceModelFactory
     */
    public function __construct(
        CustomerRepository $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\SalesrepProcessor $salesrepProcessor,
        \Magento\Customer\Model\AccountManagement $accountManager,
        \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesrepGridFactory,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helperSalesrep,
        \Appseconnect\B2BMage\Model\SalesrepFactory $salesrepModelFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory $salesrepCollectionFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        ImageProcessorInterface $imageProcessor,
        SalesrepResourceFactory $salesrepResourceModelFactory
    ) {
        $this->accountManager = $accountManager;
        $this->helperSalesrep = $helperSalesrep;
        $this->salesrepGridFactory = $salesrepGridFactory;
        $this->salesrepModelFactory = $salesrepModelFactory;
        $this->salesrepCollectionFactory = $salesrepCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->salesrepProcessor = $salesrepProcessor;
        $this->salesrepResourceModelFactory = $salesrepResourceModelFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->imageProcessor = $imageProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->eventManager = $eventManager;
    }

    /**
     * CreateAccount
     *
     * @param CustomerInterface $salesrepData SalesrepData
     * @param null              $password     Password
     * @param string            $redirectUrl  RedirectUrl
     *
     * @return CustomerInterface
     */
    public function createAccount(CustomerInterface $salesrepData, $password = null, $redirectUrl = '')
    {
        if ($password !== null) {
            $this->accountManager->checkPasswordStrength($password);
            $hash = $this->accountManager->createPasswordHash($password);
        } else {
            $hash = null;
        }
        $salesrepData->setCustomAttribute('customer_type', 2);
        if ($salesrepData->getCustomAttribute('customer_status')) {
            $salesrepData->setCustomAttribute(
                'customer_status',
                $salesrepData->getCustomAttribute('customer_status')
                    ->getValue()
            );
        }
        $customer = $this->accountManager->createAccountWithPasswordHash($salesrepData, $hash, $redirectUrl);
        if ($customer->getId()) {
            $salesrepId = $customer->getId();
            $salesrepModel = $this->salesrepGridFactory->create();
            $originalRequestData['name'] = $customer->getFirstname() . " " . $customer->getMiddlename() . " " . $customer->getLastname();
            $originalRequestData['email'] = $customer->getEmail();
            $originalRequestData['salesrep_customer_id'] = $customer->getId();
            $originalRequestData['website_id'] = $customer->getWebsiteId();
            $originalRequestData['gender'] = $customer->getGender();
            $originalRequestData['is_active'] = $customer->getCustomAttribute('customer_status')->getValue();
            $salesrepModel->setData($originalRequestData);
            $salesrepModel->save();
        }
        return $customer;
    }

    /**
     * Save
     *
     * @param CustomerInterface $salesrepData SalesrepData
     * @param null              $passwordHash PasswordHash
     *
     * @return CustomerInterface
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $salesrepData, $passwordHash = null)
    {
        $salesrepCustomerId = $salesrepData->getId();
        $salesrepIdExist = $this->salesrepGridFactory->create()
            ->getCollection()
            ->addFieldToFilter('salesrep_customer_id', $salesrepCustomerId)
            ->getData() ? true : false;
        if (!$salesrepIdExist) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Salesrep ID doesn't exist", $salesrepCustomerId)
            );
        }
        if ($salesrepData->getCustomAttribute('customer_status')) {
            $salesrepData->setCustomAttribute(
                'customer_status',
                $salesrepData->getCustomAttribute('customer_status')
                    ->getValue()
            );
        }
        if (($salesrepData->getCustomAttribute('customer_type') != null)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("[customer_type] cannot be changed")
            );
        }
        $salesrepData->setId($salesrepCustomerId);
        $prevCustomerData = null;
        if ($salesrepData->getId()) {
            $prevCustomerData = $this->customerRepository->getById($salesrepData->getId());
        }
        $salesrepData = $this->imageProcessor
            ->save($salesrepData, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $prevCustomerData);
        $origAddresses = $salesrepData->getAddresses();
        $salesrepData->setAddresses([]);
        $customerData = $this->extensibleDataObjectConverter
            ->toNestedArray($salesrepData, [], '\Magento\Customer\Api\Data\CustomerInterface');
        $salesrepData->setAddresses($origAddresses);
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
        $customerModel->setId($salesrepData->getId());

        // Need to use attribute set or future updates can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        }
        // Populate model with secure data
        if ($salesrepData->getId()) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($salesrepData->getId());
            $customerModel->setRpToken($customerSecure->getRpToken());
            $customerModel->setRpTokenCreatedAt($customerSecure->getRpTokenCreatedAt());
            $customerModel->setPasswordHash($customerSecure->getPasswordHash());
            $customerModel->setFailuresNum($customerSecure->getFailuresNum());
            $customerModel->setFirstFailure($customerSecure->getFirstFailure());
            $customerModel->setLockExpires($customerSecure->getLockExpires());
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

        $savedCustomer = $this->customerRepository->get($salesrepData->getEmail(), $salesrepData->getWebsiteId());
        $this->eventManager->dispatch(
            'customer_save_after_data_object',
            [
                'customer_data_object' => $savedCustomer,
                'orig_customer_data_object' => $salesrepData
            ]
        );
        $this->addSalesrepResentative($savedCustomer, $salesrepCustomerId);
        return $savedCustomer;
    }

    /**
     * AddSalesRepresentative
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $savedCustomer      SavedCustomer
     * @param int                                          $salesrepCustomerId SalesrepCustomerId
     *
     * @return void
     */
    public function addSalesRepresentative($savedCustomer, $salesrepCustomerId)
    {
        if ($savedCustomer->getId()) {
            $salesrepModel = $this->salesrepGridFactory->create();
            $selsrepData = $this->helperSalesrep->isSalesrep($salesrepCustomerId, true);
            $salesrepId = $selsrepData[0]['id'];
            $originalRequestData['id'] = $salesrepId;
            $originalRequestData['website_id'] = $savedCustomer->getWebsiteId();
            $savedCustFname = $savedCustomer->getFirstname();
            $savedCustMname = $savedCustomer->getMiddlename();
            $savedCustLname = $savedCustomer->getLastname();
            $savedCustFullName = $savedCustFname . " " . $savedCustMname . " " . $savedCustLname;
            $originalRequestData['name'] = $savedCustFullName;
            $originalRequestData['salesrep_customer_id'] = $savedCustomer->getId();
            $originalRequestData['is_active'] = $savedCustomer->getCustomAttribute('customer_status')->getValue();
            $originalRequestData['email'] = $savedCustomer->getEmail();
            $originalRequestData['gender'] = $savedCustomer->getGender();
            $salesrepModel->setData($originalRequestData);
            $salesrepModel->save();
        }
    }

    /**
     * AssignCustomer
     *
     * @param \Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface $requestData RequestData
     *
     * @return \Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface|array
     */
    public function assignCustomer(\Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface $requestData)
    {
        $salesrepCustomerMapModel = $this->salesrepModelFactory->create();
        $requestDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($requestData, [], 'Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface');
        if (!isset($requestDataArray['salesrep_id'])) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__("[salesrep_id] is required field"));
        }
        $selsrepData = $this->helperSalesrep->isSalesrep($requestDataArray['salesrep_id'], true);
        $selsrepId = $requestDataArray['salesrep_id'];
        $requestDataArray['salesrep_id'] = $selsrepData[0]['id'];
        if (!($this->salesrepGridFactory->create()->load($requestDataArray['salesrep_id'])->getId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Salesrep id doesn't exist", $requestDataArray['salesrep_id'])
            );
        } elseif (empty($requestDataArray['customer_ids'])) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("[customer_ids] is required field")
            );
        }
        $data = [];
        $return = [];
        $output = $this->salesrepProcessor->process($requestDataArray);
        return $output;
    }

    /**
     * GetCustomerData
     *
     * @param SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    public function getCustomerData(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->customerFactory->create()->getCollection();
        $collection->addAttributeToSelect('id');
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $result = $this->addFilterCustomerData($group, $collection);
        }
        // join Salesrep filter
        if ($result) {
            $collection = $this->salesrepResourceModelFactory->create()->getJoinSalesRepData(
                $collection,
                $result
            );
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection
                    ->addOrder(
                        $sortOrder->getField(),
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                    );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $customers = [];
        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup                 $filterGroup FilterGroup
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection  Collection
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterCustomerData(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {

        $salesrepId = '';
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            if (trim($filter->getField()) == 'salesrep_id') {
                $salesrepId = $filter->getValue();
                $salesrepCollection = $this->helperSalesrep->isSalesrep($salesrepId);
                if (!$salesrepCollection) {
                    throw new \Magento\Framework\Exception\NoSuchEntityException(
                        __("Request salesrep doesn't exist", $salesrepId)
                    );
                }
                continue;
            }
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = [
                'attribute' => $filter->getField(),
                $condition => $filter->getValue()
            ];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
        return $salesrepId;
    }

    /**
     * GetCompany
     *
     * @param int                     $id             Id
     * @param SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    public function getCompany($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->helperSalesrep->isSalesrep($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request salesrep doesn't exist", $id)
            );
        }
        $salesrepCollection = $this->salesrepGridFactory->create()
            ->getCollection()
            ->addFieldToFilter('salesrep_customer_id', $id);
        $salesrepCollection->addFieldToSelect('id');

        foreach ($salesrepCollection as $salerep) {
            $companyId = $this->salesrepModelFactory->create()->getCollection()->addFieldToSelect('customer_id')->addFieldToFilter('salesrep_id', $salerep->getId());
        }

        $collection = $this->customerFactory->create()->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $companyId->getData()));

        if ($searchCriteria) {
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            // Add filters from root filter group to the collection
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $result = $this->addFilterCustomerData($group, $collection);
            }

            $searchResults->setTotalCount($collection->getSize());
            $sortOrders = $searchCriteria->getSortOrders();
            if ($sortOrders) {
                foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                    $collection
                        ->addOrder(
                            $sortOrder->getField(),
                            ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                        );
                }
            }
            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        $customers = array();

        foreach ($collection as $customerModel) {
            $customerModelObj = $customerModel->getDataModel();

            $contactPersonIds = array();
            $contactPerson = $this->helperContactPerson->getContactPersonId($customerModel->getEntityId());
            $contactPersonIds = array_column($contactPerson, 'contactperson_id');

            if (!empty($contactPersonIds)) {
                $collection2 = $this->customerFactory->create()->getCollection()
                    ->addAttributeToFilter('entity_id', array('in' => $contactPersonIds));
                $contactPersonArray = array();
                foreach ($collection2 as $customerModel2) {
                    $contactPersonArray[] = $customerModel2->getDataModel();
                }

                $extensionAttributes = $customerModelObj->getExtensionAttributes();
                $extensionAttributes->setContactPerson($contactPersonArray);
                $customerModelObj->setExtensionAttributes($extensionAttributes);
            }

            $customers[] = $customerModelObj;
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }
}
