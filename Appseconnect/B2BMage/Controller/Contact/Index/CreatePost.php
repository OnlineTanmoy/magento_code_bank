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

namespace Appseconnect\B2BMage\Controller\Contact\Index;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
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

/**
 * Class CreatePost
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * Account manager
     *
     * @var AccountManagementInterface
     */
    public $accountManagement;

    /**
     * Address helper
     *
     * @var Address
     */
    public $addressHelper;

    /**
     * Form factory
     *
     * @var FormFactory
     */
    public $formFactory;

    /**
     * Subscriber factory
     *
     * @var SubscriberFactory
     */
    public $subscriberFactory;

    /**
     * Region interface
     *
     * @var RegionInterfaceFactory
     */
    public $regionDataFactory;

    /**
     * Address interface
     *
     * @var AddressInterfaceFactory
     */
    public $addressDataFactory;

    /**
     * Registration
     *
     * @var Registration
     */
    public $registration;

    /**
     * Customer interface
     *
     * @var CustomerInterfaceFactory
     */
    public $customerDataFactory;

    /**
     * Customer url
     *
     * @var CustomerUrl
     */
    public $customerUrl;

    /**
     * Escaper
     *
     * @var Escaper
     */
    public $escaper;

    /**
     * Customer extractor
     *
     * @var CustomerExtractor
     */
    public $customerExtractor;

    /**
     * Url model
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $urlModel;

    /**
     * Data object helper
     *
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Cookie meta data
     *
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $_cookieMetadataFactory;

    /**
     * Cookie meta data factory
     *
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $_cookieMetadataManager;

    /**
     * Contact factory
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Create post constructor
     *
     * @param Context                                         $context             context
     * @param \Appseconnect\B2BMage\Model\ContactFactory      $contactFactory      contact
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson helper contact person
     * @param Session                                         $customerSession     customer session
     * @param StoreManagerInterface                           $storeManager        store manager
     * @param AccountManagementInterface                      $accountManagement   account manager
     * @param Address                                         $addressHelper       address helper
     * @param UrlFactory                                      $urlFactory          url
     * @param FormFactory                                     $formFactory         form
     * @param SubscriberFactory                               $subscriberFactory   subscriber
     * @param RegionInterfaceFactory                          $regionDataFactory   origin data
     * @param AddressInterfaceFactory                         $addressDataFactory  address data
     * @param CustomerUrl                                     $customerUrl         customer url
     * @param Escaper                                         $escaper             escaper
     * @param CustomerExtractor                               $customerExtractor   customer extractor
     * @param DataObjectHelper                                $dataObjectHelper    data object helper
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        CustomerUrl $customerUrl,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->contactFactory = $contactFactory;
        $this->session = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->addressHelper = $addressHelper;
        $this->formFactory = $formFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerUrl = $customerUrl;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context);
    }

    /**
     * Retrieve cookie manager
     *
     * @return     \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     * @deprecated
     */
    private function _getCookieManager()
    {
        if (!$this->_cookieMetadataManager) {
            $this->_cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Stdlib\Cookie\PhpCookieManager::class);
        }
        return $this->_cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return     \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @deprecated
     */
    private function _getCookieMetadataFactory()
    {
        if (!$this->_cookieMetadataFactory) {
            $this->_cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class);
        }
        return $this->_cookieMetadataFactory;
    }

    /**
     * Create contact account action
     *
     * @return                                  void @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $originalRequestData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->getRequest()->isPost()) {
            $url = $this->urlModel->create()->getUrl(
                '*/*/index_add', [
                    '_secure' => true
                ]
            );
            $resultRedirect->setUrl($this->_redirect->error($url));
            return $resultRedirect;
        }

        $this->session->regenerateId();

        try {

            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);

            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $this->session->getBeforeAuthUrl();

            $this->_checkPasswordConfirmation($password, $confirmation);
            $customer->setCustomAttribute('customer_type', 3);

            if ($this->session->getCustomer()->getCustomerType() == 2) {
                $customer->setCustomAttribute('contactperson_role', 2);
                $customer->setCustomAttribute('customer_status', 1);
            } else {
                $customer->setCustomAttribute('contactperson_role', $originalRequestData['role']);
                $customer->setCustomAttribute('customer_status', $originalRequestData['status']);
            }

            $customer = $this->accountManagement->createAccount($customer, $password, $redirectUrl);

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

            $this->_eventManager->dispatch(
                'customer_register_success', [
                    'account_controller' => $this,
                    'customer' => $customer
                ]
            );
            $contactPersonId = $customer->getId();
            if ($this->session->getCustomer()->getCustomerType() == 2) {
                $customerId = $originalRequestData['company_id'];
            } else {
                $customerData = $this->helperContactPerson->getCustomerId($this->session->getCustomerId());
                $customerId = $customerData['customer_id'];
            }

            // contact person work
            $contactPersonData = [];
            $contactPersonData['customer_id'] = $customerId;
            $contactPersonData['contactperson_id'] = $contactPersonId;
            if ($this->session->getCustomer()->getCustomerType() == 2) {
                $contactPersonData['is_active'] = 1;
            } else {
                $contactPersonData['is_active'] = $originalRequestData['status'];
            }
            $contactModel = $this->contactFactory->create();
            $contactModel->setData($contactPersonData);
            $contactModel->save();

            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                $this->messageManager->addSuccess(
                    __(
                        'You must confirm your account.
                                                     Please check your email for the confirmation link or
                                                     <a href="%1">click here</a> for a new link.', $email
                    )
                );

                $url = $this->urlModel->create()->getUrl(
                    '*/*/index', [
                        '_secure' => true
                    ]
                );
                $resultRedirect->setUrl($this->_redirect->success($url));
            } else {
                if ($this->session->getCustomer()->getCustomerType() == 2) {
                    $this->messageManager->addSuccess(__("Contact person account has been successfully created"));
                    $url = $this->urlModel->create()->getUrl(
                        '*/salesrep/contact_create', [
                            '_secure' => true
                        ]
                    );
                    $resultRedirect->setUrl($url);
                } else {
                    $this->messageManager->addSuccess(__("Your account has been successfully created"));
                    $url = $this->urlModel->create()->getUrl(
                        '*/*/index_listing', [
                            '_secure' => true
                        ]
                    );
                    $resultRedirect->setUrl($this->_redirect->success($url));
                }
                return $resultRedirect;
            }
            if ($this->_getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->_getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->_getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }

            return $resultRedirect;
        } catch (StateException $e) {
            $forgotPwdUrl = $this->urlModel->create()->getUrl('customer/account/forgotpassword');
            $errorMessage = __(
                'There is already an account with this email address.
                         If you are sure that it is your email address,
                         <a href="%1">click here</a> to get your password and access your account.', $forgotPwdUrl
            );

            $this->messageManager->addError($errorMessage);
        } catch (InputException $e) {
            $errorMessage = $e->getMessage();
            $this->messageManager->addError($this->escaper->escapeHtml($errorMessage));
            foreach ($e->getErrors() as $error) {
                $errorMessage = $error->getMessage();
                $this->messageManager->addError($this->escaper->escapeHtml($errorMessage));
            }
        } catch (LocalizedException $e) {
            $errorMessage = $e->getMessage();
            $this->messageManager->addError($this->escaper->escapeHtml($errorMessage));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t save the contact person.'));
        }
        $postValue = $this->getRequest()->getPostValue();
        $this->session->setCustomerFormData($postValue);
        $defaultUrl = $this->urlModel->create()->getUrl(
            '*/*/index_add', [
                '_secure' => true
            ]
        );
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        return $resultRedirect;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password     password
     * @param string $confirmation confirmation
     *
     * @return void
     * @throws InputException
     */
    private function _checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    private function _getSuccessMessage()
    {
        if ($this->addressHelper->isVatValidationEnabled()) {
            if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                $message = __(
                    'If you are a registered VAT customer,
                    please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->urlModel->create()->getUrl('customer/address/edit')
                );
            } else {
                $message = __(
                    'If you are a registered VAT customer,
                    please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->urlModel->create()->getUrl('customer/address/edit')
                );
            }
        } else {
            $message = __(
                'Thank you for registering with %1.',
                $this->storeManager->getStore()->getFrontendName()
            );
        }
        return $message;
    }
}
