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

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;

/**
 * Class EditPost
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class EditPost extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * Form code for data extractor
     */
    const FORM_DATA_EXTRACTOR_CODE = 'customer_account_edit';

    /**
     * Customer account manager
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * Customer repository
     *
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * Validator
     *
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * Customer extractor
     *
     * @var CustomerExtractor
     */
    public $customerExtractor;    

    /**
     * Email notification
     *
     * @var EmailNotificationInterface
     */
    private $_emailNotification;

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * Authentication
     *
     * @var AuthenticationInterface
     */
    private $_authentication;

    /**
     * Edit post constructor
     *
     * @param Context                     $context                   context
     * @param AccountManagementInterface  $customerAccountManagement customer account manager
     * @param CustomerRepositoryInterface $customerRepository        customer repository
     * @param Validator                   $formKeyValidator          form validator
     * @param CustomerExtractor           $customerExtractor         customer extractor
     */
    public function __construct(
        Context $context,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor
    ) {
    
        parent::__construct($context);
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function _getAuthentication()
    {
        if (! ($this->_authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()
                        ->get(\Magento\Customer\Model\AuthenticationInterface::class);
        } else {
            return $this->_authentication;
        }
    }

    /**
     * Get email notification
     *
     * @return     EmailNotificationInterface
     * @deprecated
     */
    private function _getEmailNotification()
    {
        if (! ($this->_emailNotification instanceof EmailNotificationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(EmailNotificationInterface::class);
        } else {
            return $this->_emailNotification;
        }
    }

    /**
     * Change customer email or password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $originalRequestData = $this->getRequest()->getPostValue();
            $customer = $this->customerRepository->getById($originalRequestData['contactperson_id']);
            $customer->setFirstname($originalRequestData['firstname']);
            $customer->setLastname($originalRequestData['lastname']);
            if (isset($originalRequestData['email']) && $originalRequestData['email']) {
                $customer->setEmail($originalRequestData['email']);
            }
            $customer->setCustomAttribute('contactperson_role', $originalRequestData['role']);
            $customer->setCustomAttribute('customer_status', $originalRequestData['status']);
            $this->customerRepository->save($customer);
            $this->messageManager->addSuccess(__('You saved the account information.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
        }
        return $resultRedirect->setPath('*/*/index_listing');
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     */
    private function _getScopeConfig()
    {
        if (! ($this->_scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()
                        ->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        } else {
            return $this->_scopeConfig;
        }
    }

    /**
     * Account editing action completed successfully event
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject customer data
     *
     * @return void
     */
    private function _dispatchSuccessEvent(\Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject)
    {
        $this->_eventManager->dispatch(
            'customer_account_edited', [
            'email' => $customerCandidateDataObject->getEmail()
            ]
        );
    }

    /**
     * Get customer data object
     *
     * @param int $customerId customer id
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function _getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param \Magento\Framework\App\RequestInterface      $inputData           input data
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData current customer data
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function _populateNewCustomerDataObject(
        \Magento\Framework\App\RequestInterface $inputData,
        \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
    ) {
    
        $customerDto = $this->customerExtractor->extract(self::FORM_DATA_EXTRACTOR_CODE, $inputData);
        $customerDto->setId($currentCustomerData->getId());
        if (! $customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (! $inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }
        
        return $customerDto;
    }
}
