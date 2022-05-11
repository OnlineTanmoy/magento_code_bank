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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Salesrep;

use Magento\Customer\Api\Data\CustomerInterface;
use Appseconnect\B2BMage\Model\SalesrepgridFactory;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Message\Error;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Customer\Model\AddressRegistry;

/**
 * Class Save
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
     * File
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Address model
     *
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressFactory;

    /**
     * Form model
     *
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    public $formFactory;

    /**
     * Subscriber
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
     * Math random
     *
     * @var \Magento\Framework\Math\Random
     */
    public $mathRandomFramework;


    /**
     * Customer repository
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
     * @var $addressMapper
     */
    public $addressMapper;


    /**
     * Customer account management
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;


    /**
     * Address repository interface
     *
     * @var AddressRepositoryInterface
     */
    public $addressRepository;


    /**
     * Customer data model
     *
     * @var CustomerInterfaceFactory
     */
    public $customerDataFactory;


    /**
     * Address data model
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
     * Object model
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
     * Result Page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;


    /**
     * Result page
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
     * Salesrep grid collection
     *
     * @var SalesrepgridFactory
     */
    public $salesRepGridFactory;

    /**
     * Random
     *
     * @var \Magento\Framework\Math\Random
     */
    public $random;

    /**
    /**
     * Save constructor.
     *
     * @param Action\Context                                       $actionContext                 context
     * @param \Magento\Framework\Registry                          $coreRegistry                  core registry
     * @param \Magento\Framework\App\Response\Http\FileFactory     $fileFactory                   file
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory               customer model
     * @param \Magento\Customer\Model\AddressFactory               $addressFactory                address model
     * @param \Magento\Customer\Model\Metadata\FormFactory         $formFactory                   form model
     * @param \Magento\Newsletter\Model\SubscriberFactory          $subscriberFactory             subscriber model
     * @param \Magento\Customer\Helper\View                        $viewCustomerHelper            customer view helper
     * @param \Magento\Framework\Math\Random                       $mathRandomFramework           math random
     * @param CustomerRepositoryInterface                          $customerRepository            customer repository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter extensible data object converter
     * @param Mapper                                               $addressMapper                 address mapper
     * @param AccountManagementInterface                           $customerAccountManagement     customer account management
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
     * @param SalesrepgridFactory                                  $salesRepGridFactory           salesrep grid
     * @param SubscriptionManagerInterface                         $subscriptionManager           subscription manager
     * @param AddressRegistry                                      $addressRegistry               address registry
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
        \Magento\Framework\Math\Random $mathRandomFramework,
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
        SalesrepgridFactory $salesRepGridFactory,
        SubscriptionManagerInterface $subscriptionManager,
        AddressRegistry $addressRegistry = null
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerDataFactory = $customerDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerMapper = $customerMapper;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->salesRepGridFactory = $salesRepGridFactory;
        parent::__construct(
            $actionContext,
            $coreRegistry,
            $fileFactory,
            $customerFactory,
            $addressFactory,
            $formFactory,
            $subscriberFactory,
            $viewCustomerHelper,
            $mathRandomFramework,
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
     * @return                                        \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $customerId = isset($originalRequestData['customer']['entity_id']) ?
            $originalRequestData['customer']['entity_id'] :
            null;
        if ($originalRequestData) {
            try {
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData($customerData);
                $request = $this->getRequest();
                $isExistingCustomer = (bool) $customerId;
                $customer = $this->customerDataFactory->create();
                if ($isExistingCustomer) {
                    $savedCustomerData = $this->customerRepository->getById($customerId);
                    $customerData = array_merge($this->customerMapper->toFlatArray($savedCustomerData), $customerData);
                    $customerData['id'] = $customerId;
                }

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
                $customer->setAddresses($processedAddresses);
                $customer->setCustomAttribute('customer_type', 2);
                $customer->setStoreId($customerData['sendemail_store_id']);

                $customerId = $this->_processSalesRep($isExistingCustomer, $customer, $customerId);

                $this->_processSubscription($customerId);

                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after', [
                        'customer' => $customer,
                        'request' => $request
                    ]
                );

                $this->_saveSalesRepMapping($originalRequestData, $customerId);
                $this->_getSession()->unsCustomerData();
                $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
                $this->messageManager->addSuccess(__('You saved the sales representative.'));
                $returnToEdit = (bool) $this->getRequest()->getParam('back', false);
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
                    __('Something went wrong while saving sales representative.')
                );
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($customerId) {
                $resultRedirect->setPath(
                    'b2bmage/salesrep/edit', [
                        'id' => $customerId,
                        '_current' => false
                    ]
                );
            } else {
                $resultRedirect->setPath(
                    'b2bmage/salesrep/new', [
                        '_current' => false
                    ]
                );
            }
        } else {
            $resultRedirect->setPath('b2bmage/salesrep/index');
        }
        return $resultRedirect;
    }

    /**
     * Process subscrib
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
     * Process sales rep
     *
     * @param boolean                  $isExistingCustomer is existing customer
     * @param CustomerInterfaceFactory $customer           customer
     * @param int                      $customerId         customer id
     *
     * @return int
     */
    private function _processSalesRep($isExistingCustomer, $customer, $customerId)
    {
        if ($isExistingCustomer) {
            $this->customerRepository->save($customer);
        } else {
            $customer = $this->customerAccountManagement->createAccount($customer);
            $customerId = $customer->getId();
        }

        return $customerId;
    }

    /**
     * Save salesrep mappng
     *
     * @param array $originalRequestData original request data
     * @param int   $customerId          customer id
     *
     * @return void
     */
    private function _saveSalesRepMapping($originalRequestData, $customerId)
    {
        $salesrepId = $this->_getSession()->getSalesrepId();
        $salesrepModel = $this->salesRepGridFactory->create();
        $originalRequestData['customer']['name'] = $originalRequestData['customer']['firstname'] . " " .
            $originalRequestData['customer']['middlename'] . " " .
            $originalRequestData['customer']['lastname'];
        $originalRequestData['customer']['salesrep_customer_id'] = $customerId;
        $salesrepModel->setData($originalRequestData['customer']);
        if (isset($originalRequestData['customer']['entity_id'])
            && $originalRequestData['customer']['entity_id'] != ''
        ) {
            $salesrepModel->setData('id', $salesrepId);
        }
        $salesrepModel->save();
    }

    /**
     * Process address
     *
     * @param array $addressesData address data
     *
     * @return array
     */
    private function _processAddress($addressesData)
    {
        $addresses = [];
        foreach ($addressesData as $addressData) {
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
