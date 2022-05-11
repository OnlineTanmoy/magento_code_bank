<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\CompanyDivision\Controller\Adminhtml\Division;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Message\Error;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Customer\Model\AddressRegistry;

/**
 * Class Redirect
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index\Save
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * File factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Customer address
     *
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressFactory;

    /**
     * Customer form
     *
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    public $formFactory;

    /**
     * Newsletter subscriber
     *
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    public $subscriberFactory;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    public $viewCustomerHelper;

    /**
     * Random
     *
     * @var \Magento\Framework\Math\Random
     */
    public $random;

    /**
     * Customer repository interface
     *
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * Extensible data object converter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * Address mapper
     *
     * @var Mapper
     */
    public $addressMapper;

    /**
     * Customer account manager
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * Address repository
     *
     * @var AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * Customer interface
     *
     * @var CustomerInterfaceFactory
     */
    public $customerDataFactory;

    /**
     * Address interface
     *
     * @var AddressInterfaceFactory
     */
    public $addressDataFactory;

    /**
     * Customer mapper
     *
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    public $customerMapper;

    /**
     * Data object processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * Object factory
     *
     * @var ObjectFactory
     */
    public $objectFactory;

    /**
     * Data object helper
     *
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * Result layout
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Result forward
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    public $resultForwardFactory;

    /**
     * Result json
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Contact
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Contact persion helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                  $actionContext                 action context
     * @param \Magento\Framework\Registry                          $coreRegistry                  core registry
     * @param \Magento\Framework\App\Response\Http\FileFactory     $fileFactory                   file
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory               customer object
     * @param \Magento\Customer\Model\AddressFactory               $addressFactory                address
     * @param \Magento\Customer\Model\Metadata\FormFactory         $formFactory                   form factory
     * @param \Magento\Newsletter\Model\SubscriberFactory          $subscriberFactory             newsletter subscriber
     * @param \Magento\Customer\Helper\View                        $viewCustomerHelper            customer view helper
     * @param \Magento\Framework\Math\Random                       $random                        random
     * @param CustomerRepositoryInterface                          $customerRepository            customer repository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter extensible data object converter
     * @param Mapper                                               $addressMapper                 address mapper
     * @param AccountManagementInterface                           $customerAccountManagement     customer account manager
     * @param AddressRepositoryInterface                           $addressRepository             address repository
     * @param CustomerInterfaceFactory                             $customerDataFactory           customer data
     * @param AddressInterfaceFactory                              $addressDataFactory            address data
     * @param \Magento\Customer\Model\Customer\Mapper              $customerMapper                customer mapper
     * @param \Magento\Framework\Reflection\DataObjectProcessor    $dataObjectProcessor           data object processor
     * @param DataObjectHelper                                     $dataObjectHelper              data object helper
     * @param ObjectFactory                                        $objectFactory                 object factory
     * @param \Magento\Framework\View\LayoutFactory                $layoutFactory                 layout
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory           result layout
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory             result page
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory          result forward
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory             result json
     * @param \Appseconnect\B2BMage\Model\ContactFactory           $contactFactory                contact
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data      $helperContactPerson           contact person helper
     * @param SubscriptionManagerInterface                         $subscriptionManager           subscription manager
     * @param AddressRegistry|null                                 $addressRegistry               address registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $actionContext,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $viewCustomerHelper,
        \Magento\Framework\Math\Random $random,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Mapper $addressMapper,
        AccountManagementInterface $customerAccountManagement,
        AddressRepositoryInterface $addressRepository,
        CustomerInterfaceFactory $customerDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        ObjectFactory $objectFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Appseconnect\CompanyDivision\Model\DivisionFactory $divisionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        SubscriptionManagerInterface $subscriptionManager,
        AddressRegistry $addressRegistry = null
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customerFactory = $customerFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->viewCustomerHelper = $viewCustomerHelper;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerDataFactory = $customerDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerMapper = $customerMapper;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->divisionFactory = $divisionFactory;
        $this->helperContactPerson = $helperContactPerson;
        parent::__construct(
            $actionContext,
            $coreRegistry,
            $fileFactory,
            $customerFactory,
            $addressFactory,
            $formFactory,
            $subscriberFactory,
            $viewCustomerHelper,
            $random,
            $customerRepository,
            $extensibleDataObjectConverter,
            $addressMapper,
            $customerAccountManagement,
            $addressRepository,
            $customerDataFactory,
            $addressDataFactory,
            $customerMapper,
            $dataObjectProcessor,
            $dataObjectHelper,
            $objectFactory,
            $layoutFactory,
            $resultLayoutFactory,
            $resultPageFactory,
            $resultForwardFactory,
            $resultJsonFactory,
            $subscriptionManager,
            $addressRegistry
        );
    }


    /**
     * Save customer action
     *
     * @return                                        \Magento\Backend\Model\View\Result\Redirect @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $returnToEdit = false;
        $customerId = $this->_getSession()->getCustomerId();
        $originalRequestData = $this->getRequest()->getPostValue();
        $entityId = $this->_getSession()->getDivisionId();

        $customerData = $this->helperContactPerson->checkCustomerStatus($customerId, true);
        $customerWebsiteId = $customerData['website_id'];

        $divisionId = isset($originalRequestData['customer']['entity_id']) ?
            $originalRequestData['customer']['entity_id'] :
            null;

        if ($originalRequestData) {
            try {
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData($customerData);
                $request = $this->getRequest();
                $isExistingCustomer = (bool)$divisionId;
                $customer = $this->customerDataFactory->create();
                if ($isExistingCustomer) {
                    $savedCustomerData = $this->customerRepository->getById($divisionId);
                    $customerData = array_merge($this->customerMapper->toFlatArray($savedCustomerData), $customerData);
                    $customerData['id'] = $divisionId;
                }

                $customerData['customer_type'] = 4;

                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );
                $processedAddresses = $this->_processAddress($addressesData);

                $this->_eventManager->dispatch(
                    'adminhtml_customer_prepare_save', [
                        'customer' => $customer,
                        'request' => $request
                    ]
                );

                $this->_setParentCustomerAttributes(
                    $customer,
                    $customerId,
                    $processedAddresses,
                    $customerData,
                    $customerWebsiteId
                );

                $divisionId = $this->_processDivision($isExistingCustomer, $customer, $divisionId);

                $this->_saveSoldToAddress($customer, $divisionId);

                $this->_processSubscription($customerId);

                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after', [
                        'customer' => $customer,
                        'request' => $request
                    ]
                );
                $parent_customer_id = $this->_getSession()->getCustomerId();
                $this->_getSession()->unsCustomerData();

                $this->_saveDivisionMapping($originalRequestData, $customerId, $divisionId, $entityId);

                $this->_getSession()->unsCustomerId();
                $this->_getSession()->unsDivisionId();

                $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $divisionId);
                $this->messageManager->addSuccess(__('You saved the division.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->_addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException(
                    $exception,
                    __('Something went wrong while saving the contact person.')
                );
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($divisionId) {
                $resultRedirect->setPath(
                    'division/division/edit', [
                        'id' => $divisionId,
                        'division_id' => $entityId,
                        'customer_id' => $customerId,
                        'is_division' => 1,
                        '_current' => false
                    ]
                );
            } else {
                $resultRedirect->setPath(
                    'division/division/edit', [
                        'customer_id' => $customerId,
                        'is_division' => 1,
                        '_current' => true
                    ]
                );
            }
        } else {
            $resultRedirect->setPath(
                'customer/index/edit', [
                    'id' => $customerId,
                    '_current' => true
                ]
            );
        }
        return $resultRedirect;
    }

    /**
     * Process subscribe
     *
     * @param int $customerId customer id
     *
     * @return void
     */
    private function _processSubscription($customerId)
    {
        $isSubscribed = null;
        if ($this->_authorization->isAllowed(null)) {
            $isSubscribed = $this->getRequest()->getPost('subscription');
        }
        if ($isSubscribed !== null) {
            if ($isSubscribed !== 'false') {
                $this->subscriberFactory->create()->subscribeCustomerById($customerId);
            } else {
                $this->subscriberFactory->create()->unsubscribeCustomerById($customerId);
            }
        }
    }

    /**
     * Process contact person
     *
     * @param boolean                  $isExistingCustomer is exits customer
     * @param CustomerInterfaceFactory $customer           customer interface
     * @param int                      $contactPersonId    contact person id
     *
     * @return int
     */
    private function _processDivision($isExistingCustomer, $customer, $divisionId)
    {
        if ($isExistingCustomer) {
            $this->customerRepository->save($customer);
            $divisionId = $customer->getId();
        } else {

            $customer = $this->customerAccountManagement->createAccount($customer);
            $divisionId = $customer->getId();
        }

        return $divisionId;
    }

    /**
     * Save sold to address
     *
     * @param CustomerInterfaceFactory $customer        customer interface
     * @param int                      $contactPersonId contact person id
     *
     * @return void
     */
    private function _saveSoldToAddress($customer, $divisionId)
    {
        $customerDetail = $this->customerFactory->create()->load($divisionId);
        $addressId = null;
        if ($this->soldTo >= 0) {
            foreach ($customer->getAddresses() as $key => $val) {
                if ($key == $this->soldTo) {
                    $addressId = $val->getId();
                }
            }
        }

        $customerDetail->setDefaultSold($addressId);
        $customerDetail->setCustomerType(4);
        $customerDetail->save();
    }

    /**
     * Contact person mapping
     *
     * @param array $originalRequestData original request data
     * @param int   $customerId          customer id
     * @param int   $contactPersonId     contact person id
     * @param int   $entityId            entity id
     *
     * @return void
     */
    private function _saveDivisionMapping($originalRequestData, $customerId, $divisionId, $entityId)
    {
        $divisionDetail = $this->customerFactory->create()->load($divisionId);
        $divisionData = [];
        $divisionData['customer_id'] = $customerId;
        $divisionData['division_id'] = $divisionId;
        $divisionData['is_active'] = $originalRequestData['customer']['customer_status'];
        $divisionData['name'] = $divisionDetail->getName();
        $divisionData['email'] = $divisionDetail->getEmail();
        $divisionModel = $this->divisionFactory->create();
        $divisionModel->setData($divisionData);
        if (isset($originalRequestData['customer']['entity_id'])
            && $originalRequestData['customer']['entity_id'] != ''
        ) {
            $divisionData['id'] = $entityId;
            $divisionModel->addData($divisionData);
        } else {
            $divisionModel->setData($divisionData);
        }
        $divisionModel->save();
    }

    /**
     * Save parent customer attribute
     *
     * @param CustomerInterfaceFactory $customer          customer interface
     * @param int                      $customerId        customer id
     * @param mixed                    $addresses         address
     * @param array                    $customerData      customer data
     * @param int                      $customerWebsiteId customer website id
     *
     * @return void
     */
    private function _setParentCustomerAttributes(
        $customer,
        $customerId,
        $addresses,
        $customerData,
        $customerWebsiteId
    ) {
        $parentCustomerGroupId = $this->customerFactory->create()
            ->load($customerId)
            ->getGroupId();
        $customer->setAddresses($addresses);
        $customer->setCustomAttribute('customer_type', 1);
        $customer->setStoreId($customerData['sendemail_store_id']);
        $customer->setGroupId($parentCustomerGroupId);
        $customer->setWebsiteId($customerWebsiteId);
    }

    /**
     * Address process
     *
     * @param array $addressesData address data
     *
     * @return array
     */
    private function _processAddress($addressesData)
    {
        $this->soldTo = -1;
        $addresses = [];
        foreach ($addressesData as $addressKey => $addressData) {
            if (isset($addressData['default_sold']) && $addressData['default_sold'] != '') {
                $this->soldTo = $addressKey;
            }

            $region = isset($addressData['region']) ? $addressData['region'] : null;
            $regionId = isset($addressData['region_id']) ? $addressData['region_id'] : null;
            $addressData['region'] = [
                'region' => $region,
                'region_id' => $regionId
            ];
            $addressDataObject = $this->addressDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $addressDataObject,
                $addressData,
                '\Magento\Customer\Api\Data\AddressInterface'
            );
            $addresses[] = $addressDataObject;
        }
        return $addresses;
    }
}
