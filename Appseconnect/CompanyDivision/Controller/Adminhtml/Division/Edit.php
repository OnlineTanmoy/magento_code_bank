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
use Magento\Catalog\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
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

/**
 * Class Edit
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Catalog session
     *
     * @var Session
     */
    public $catalogSession;

    /**
     * Http file factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $httpFileFactory;

    /**
     * Customer model factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerModelFactory;

    /**
     * Address model factory
     *
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressModelFactory;

    /**
     * Form meta data factory
     *
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    public $formMetaDataFactory;

    /**
     * Subscriber model factory
     *
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    public $subscriberModelFactory;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    public $viewCustomerHelper;

    /**
     * Random framework
     *
     * @var \Magento\Framework\Math\Random
     */
    public $randomFramework;

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
     * Customer data factory
     *
     * @var CustomerInterfaceFactory
     */
    public $customerDataFactory;

    /**
     * Address data factory
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
     * Data object provessor
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
     * Layout factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * Result layout factory
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Result forward factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    public $resultForwardFactory;

    /**
     * Result json factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Contact factory
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Helper contact person
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context                  $actionContext                 action context
     * @param Session                                              $catalogSession                catalog session
     * @param \Magento\Framework\Registry                          $registry                      registry
     * @param \Magento\Framework\App\Response\Http\FileFactory     $httpFileFactory               http file factory
     * @param \Magento\Customer\Model\CustomerFactory              $customerModelFactory          customer model factory
     * @param \Magento\Customer\Model\AddressFactory               $addressModelFactory           address model factory
     * @param \Magento\Customer\Model\Metadata\FormFactory         $formMetaDataFactory           form meta data factory
     * @param \Magento\Newsletter\Model\SubscriberFactory          $subscriberModelFactory        subscriber model factory
     * @param \Magento\Customer\Helper\View                        $viewCustomerHelper            view customer helper
     * @param \Magento\Framework\Math\Random                       $randomFramework               random framework
     * @param CustomerRepositoryInterface                          $customerRepository            customer repository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter extensible data object converter
     * @param Mapper                                               $addressMapper                 address mapper
     * @param AccountManagementInterface                           $customerAccountManagement     custommer account manager
     * @param AddressRepositoryInterface                           $addressRepository             address repository
     * @param CustomerInterfaceFactory                             $customerDataFactory           customer data factory
     * @param AddressInterfaceFactory                              $addressDataFactory            address data factory
     * @param \Magento\Customer\Model\Customer\Mapper              $customerMapper                customer mapper
     * @param \Magento\Framework\Reflection\DataObjectProcessor    $dataObjectProcessor           data object processor
     * @param DataObjectHelper                                     $dataObjectHelper              data object helper
     * @param ObjectFactory                                        $objectFactory                 object factory
     * @param \Magento\Framework\View\LayoutFactory                $layoutFactory                 lauout factory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory           result layout factory
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory             result page factory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory          result forward factory
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory             result json factory
     * @param \Appseconnect\B2BMage\Model\ContactFactory           $contactFactory                contact factory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data      $helperContactPerson           helper contact person
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $actionContext,
        Session $catalogSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http\FileFactory $httpFileFactory,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Customer\Model\AddressFactory $addressModelFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formMetaDataFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberModelFactory,
        \Magento\Customer\Helper\View $viewCustomerHelper,
        \Magento\Framework\Math\Random $randomFramework,
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
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    ) {
        $this->catalogSession = $catalogSession;
        $this->formMetaDataFactory = $formMetaDataFactory;
        $this->viewCustomerHelper = $viewCustomerHelper;
        $this->customerRepository = $customerRepository;
        $this->addressMapper = $addressMapper;
        $this->addressRepository = $addressRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerMapper = $customerMapper;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->contactFactory = $contactFactory;
        $this->helperContactPerson = $helperContactPerson;
        parent::__construct(
            $actionContext,
            $registry,
            $httpFileFactory,
            $customerModelFactory,
            $addressModelFactory,
            $formMetaDataFactory,
            $subscriberModelFactory,
            $viewCustomerHelper,
            $randomFramework,
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
            $resultJsonFactory
        );
    }

    /**
     * Action function
     *
     * @return void
     */
    public function execute()
    {
        $customerId = $this->initCurrentCustomer();
        $this->catalogSession->unsParentCustomerId();
        $this->catalogSession->setParentCustomerId(
            $this->getRequest()
                ->getParam('customer_id')
        );
        $this->_getSession()->setCustomerId(
            $this->getRequest()
                ->getParam('customer_id')
        );
        $this->_getSession()->setDivisionId(
            $this->getRequest()
                ->getParam('division_id')
        );
        $customerData = [];
        $customerData['account'] = [];
        $customerData['address'] = [];
        $customer = null;
        $isExistingCustomer = (bool)$customerId;
        if ($isExistingCustomer) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $customerData['account'] = $this->customerMapper->toFlatArray($customer);
                $customerData['account'][CustomerInterface::ID] = $customerId;
                try {
                    $addresses = $customer->getAddresses();
                    foreach ($addresses as $address) {
                        $customerData['address'][$address->getId()] = $this->addressMapper->toFlatArray($address);
                        $customerData['address'][$address->getId()]['id'] = $address->getId();
                    }
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addException(
                        $e,
                        __('Something went wrong while editing the contact person.')
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('customer/*/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException($e, __('Something went wrong while editing the contact person.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/*/index');
                return $resultRedirect;
            }
        }
        $customerData['customer_id'] = $customerId;

        $data = $this->_getSession()->getCustomerData(true);

        if ($data && (!isset($data['customer_id'])
            || isset($data['customer_id'])
            && $data['customer_id'] == $customerId)
        ) {
            $this->_processAddress($data, $customer, $customerData);
        }

        $this->_getSession()->setCustomerData($customerData);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer_manage');
        $this->prepareDefaultCustomerTitle($resultPage);
        $resultPage->setActiveMenu('Magento_Customer::customer');
        if ($isExistingCustomer) {
            $resultPage->getConfig()
                ->getTitle()
                ->prepend($this->viewCustomerHelper->getCustomerName($customer));
        } else {
            $resultPage->getConfig()
                ->getTitle()
                ->prepend(__('New Division'));
        }
        return $resultPage;
    }

    /**
     * Process Address
     *
     * @param $data         post data
     * @param $customer     customer object
     * @param $customerData customer data
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _processAddress($data, $customer, $customerData)
    {
        if ($customer) {
            $customerId = $customer->getId();
        }
        $request = clone $this->getRequest();
        $request->setParams($data);

        if (isset($data['account']) && is_array($data['account'])) {
            $customerForm = $this->formMetaDataFactory->create(
                'customer',
                'adminhtml_customer',
                $customerData['account'],
                true
            );
            $formData = $customerForm->extractData($request, 'account');
            $customerData['account'] = $customerForm->restoreData($formData);
            $customer = $this->customerDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $customer,
                $customerData['account'],
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        }

        if (isset($data['address']) && is_array($data['address'])) {
            foreach (array_keys($data['address']) as $addressId) {
                if ($addressId == '_template_') {
                    continue;
                }

                try {
                    $address = $this->addressRepository->getById($addressId);
                    if (empty($customerId) || $address->getCustomerId() != $customerId) {
                        $address = $this->addressDataFactory->create();
                    }
                } catch (NoSuchEntityException $e) {
                    $address = $this->addressDataFactory->create();
                    $address->setId($addressId);
                }
                if (!empty($customerId)) {
                    $address->setCustomerId($customerId);
                }
                $address->setIsDefaultBilling(
                    !empty($data['account'][CustomerInterface::DEFAULT_BILLING])
                    && $data['account'][CustomerInterface::DEFAULT_BILLING] == $addressId
                );
                $address->setIsDefaultShipping(
                    !empty($data['account'][CustomerInterface::DEFAULT_SHIPPING])
                    && $data['account'][CustomerInterface::DEFAULT_SHIPPING] == $addressId
                );
                $requestScope = sprintf('address/%s', $addressId);
                $addressForm = $this->formMetaDataFactory->create(
                    'customer_address',
                    'adminhtml_customer_address',
                    $this->addressMapper->toFlatArray($address)
                );
                $formData = $addressForm->extractData($request, $requestScope);
                $customerData['address'][$addressId] = $addressForm->restoreData($formData);
                $customerData['address'][$addressId][\Magento\Customer\Api\Data\AddressInterface::ID] = $addressId;
            }
        }
    }
}
