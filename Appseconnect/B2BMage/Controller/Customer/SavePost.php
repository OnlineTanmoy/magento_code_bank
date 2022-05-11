<?php

namespace Appseconnect\B2BMage\Controller\Customer;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Action;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SavePost extends Action\Action
{
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $addressHelper;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Customer\Api\Data\RegionInterfaceFactory
     */
    protected $regionDataFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressDataFactory;

    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Customer\Model\CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    private $contactPersonHelper;

    protected $encryptor;

    /**
     * SavePost constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerUrl $customerUrl
     * @param Registration $registration
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountRedirect $accountRedirect
     * @param \Appseconnect\B2BMage\Model\ContactFactory $contactFactory
     * @param Validator|null $formKeyValidator
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
     */
    public function __construct(
        Context                                          $context,
        Session                                          $customerSession,
        ScopeConfigInterface                             $scopeConfig,
        StoreManagerInterface                            $storeManager,
        AccountManagementInterface                       $accountManagement,
        Address                                          $addressHelper,
        UrlFactory                                       $urlFactory,
        FormFactory                                      $formFactory,
        SubscriberFactory                                $subscriberFactory,
        RegionInterfaceFactory                           $regionDataFactory,
        AddressInterfaceFactory                          $addressDataFactory,
        CustomerInterfaceFactory                         $customerDataFactory,
        CustomerUrl                                      $customerUrl,
        Registration                                     $registration,
        Escaper                                          $escaper,
        CustomerExtractor                                $customerExtractor,
        DataObjectHelper                                 $dataObjectHelper,
        AccountRedirect                                  $accountRedirect,
        \Appseconnect\B2BMage\Model\ContactFactory       $contactFactory,
        Validator                                        $formKeyValidator = null,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data  $contactPersonHelper,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        EncryptorInterface                               $encryptor
    )
    {
        $this->session = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->addressHelper = $addressHelper;
        $this->formFactory = $formFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerUrl = $customerUrl;
        $this->registration = $registration;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        $this->contactFactory = $contactFactory;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->addressRepository = $addressRepository;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get( Validator::class );
        $this->encryptor = $encryptor;
        parent::__construct( $context );
    }

    /**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     * @deprecated 100.1.0
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @deprecated 100.1.0
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress()
    {
        $addressForm = $this->formFactory->create( 'customer_address', 'customer_register_address' );
        $allowedAttributes = $addressForm->getAllowedAttributes();
        $addresses = [];
        $postData = $this->getRequest()->getParams();

        if (isset( $postData['address'] )) {
            $regionDataObject = $this->regionDataFactory->create();
            $addressData = array();
            foreach ($allowedAttributes as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                $value = "";
                if ($attributeCode == "postcode") {
                    $value = $this->getRequest()->getParam( 'postcode' );
                }
                if ($attributeCode == "city") {
                    $value = $this->getRequest()->getParam( 'city' );
                }
                if ($attributeCode == "street") {
                    $value = $this->getRequest()->getParam( 'address' )['street'];
                }
                if ($attributeCode == "telephone") {
                    $value = $this->getRequest()->getParam( 'telephone' );
                }
                if ($attributeCode == "region_id") {
                    $value = $this->getRequest()->getParam( 'region_id' );
                }
                if ($attributeCode == "region") {
                    $value = $this->getRequest()->getParam( 'region' );
                }
                if ($attributeCode == "country_id") {
                    $country = (isset( $address["country"] )) ? $address["country"] : $this->getRequest()->getParam( 'country_id' );
                    $value = $country;
                }
                if ($attributeCode == "firstname" || $attributeCode == "lastname") {
                    $value = $postData[$attributeCode];
                }

                if ($attributeCode == "company_firstname") {
                    $value = $postData[$attributeCode];
                } else {
                    if (isset( $address[$attributeCode] )) $value = $address[$attributeCode];
                }
                if ($value === null) {
                    continue;
                }
                switch ($attributeCode) {
                    case 'region_id':
                        $regionDataObject->setRegionId( $value );
                        break;
                    case 'region':
                        $regionDataObject->setRegion( $value );
                        break;
                    default:
                        $addressData[$attributeCode] = $value;
                }
            }
            if (isset( $postData['address']["default_shipping"] )) {
                $addressData['default_shipping'] = 1;
            }
            if (isset( $postData['address']["default_billing"] )) {
                $addressData['default_billing'] = 1;
            }
            $addressData['save_in_address_book'] = 1;

            $addressDataObject = $this->addressDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $addressDataObject,
                $addressData,
                \Magento\Customer\Api\Data\AddressInterface::class
            );
            $addressDataObject->setRegion( $regionDataObject );
            $addresses[] = $addressDataObject;
        }

        return $addresses;
    }

    /**
     * Create customer account action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->session->isLoggedIn()) {
            $resultRedirect->setPath( 'customer/account' );
            return $resultRedirect;

        }
        try {
            $email = $this->getRequest()->getParam( 'email' );
            $this->session->regenerateId();
            $address = $this->extractAddress();
            $addresses = $address === null ? [] : $address;

            $customer = $this->customerExtractor->extract( 'customer_account_create', $this->_request );
            $customer->setAddresses( $addresses );

            $companyFname = $this->getRequest()->getParam( 'company_firstname' );
            $companyEmail = $this->getRequest()->getParam( 'company_email' );
            $companyTelephone = $this->getRequest()->getParam( 'telephone' );
            $dob = date( 'Y-m-d', strtotime( $this->getRequest()->getParam( 'dob' ) ) );
            $password = $this->getRequest()->getParam( 'password' );
            $confirmation = $this->getRequest()->getParam( 'password_confirmation' );
            $redirectUrl = $this->session->getBeforeAuthUrl();



            $this->checkPasswordConfirmation( $password, $confirmation );

            $customer->setEmail( $companyEmail );
            $customer->setFirstname( $companyFname );
            $customer->setLastname( "." );
            $customer->setCustomAttribute( 'customer_telephone', $companyTelephone );
            $customer->setdob( $dob );
            $customer->setCustomAttribute( 'customer_type', 4 );
            $autoapproval = $this->scopeConfig->getValue(
                'insync_b2baccount/createb2b/enable_approval',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($autoapproval == 1) {
                $customer->setCustomAttribute( 'customer_status', 1 );
            } else {
                $customer->setCustomAttribute( 'customer_status', 0 );
            }
            $customer = $this->accountManagement->createAccount( $customer, $password );


            //contact work
            $contactPerson = $this->customerExtractor->extract( 'customer_account_create', $this->_request );
            $contactPerson->setEmail( $email );
            $contactPerson->setCustomAttribute( 'customer_status', 1 );
            $contactPerson->setCustomAttribute( 'customer_type', 3 );
            $contactPerson->setCustomAttribute( 'contactperson_role', 1 );
            $contactPerson = $this->accountManagement->createAccount( $contactPerson, $password );


            $this->saveContactPersonMapping( $customer->getId(), $contactPerson->getId() );
            $this->messageManager->addSuccess(
                __(
                    'B2B customer has been created.',
                    $email
                )
            );
//             @codingStandardsIgnoreEnd
            $url = $this->urlModel->getUrl( 'b2bmage/customer/register', [] );
            $resultRedirect->setUrl( $this->_redirect->success( $url ) );
            return $resultRedirect;

        } catch (StateException $e) {
            $url = $this->urlModel->getUrl( 'b2bmage/customer/register' );
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError( $message );
        } catch (InputException $e) {
            $this->messageManager->addError( $this->escaper->escapeHtml( $e->getMessage() ) );
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError( $this->escaper->escapeHtml( $error->getMessage() ) );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError( $this->escaper->escapeHtml( __( 'Correct address not provided.' ) ) );
        } catch (\Exception $e) {
            $this->messageManager->addException( $e, __( 'We can\'t save the customer.' ) );
        }

        $this->session->setCustomerFormData( $this->getRequest()->getPostValue() );
        $defaultUrl = $this->urlModel->getUrl( 'b2bmage/customer/register', ['_secure' => true] );
        $resultRedirect->setUrl( $this->_redirect->error( $defaultUrl ) );
        return $resultRedirect;


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
        $contactModel->setData( $contactPersonData );
        $contactModel->save();
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException( __( 'Please make sure your passwords match.' ) );
        }
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->addressHelper->isVatValidationEnabled()) {
            if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->urlModel->getUrl( 'customer/address/edit' )
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->urlModel->getUrl( 'customer/address/edit' )
                );
                // @codingStandardsIgnoreEnd
            }
        } else {
            $message = __( 'Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName() );
        }
        return $message;
    }
}
