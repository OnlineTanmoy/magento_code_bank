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
namespace Appseconnect\B2BMage\Model;

use Appseconnect\B2BMage\Api\ContactPerson\ContactPersonRepositoryInterface;
use Appseconnect\B2BMage\Api\ContactPerson\Data\ContactPersonExtendInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Eav\Model\Validator\Attribute\Backend;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory;

/**
 * Class AccountManagement
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AccountManagement implements ContactPersonRepositoryInterface
{
    /**
     * Contact
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contact;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * AccountManagement constructor.
     *
     * @param ContactFactory                            $contact           contact
     * @param \Magento\Customer\Model\AccountManagement $accountManagement account management
     * @param CustomerFactory                           $customerFactory   customer
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ContactFactory $contact,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        CustomerFactory $customerFactory
    ) {
        $this->contact = $contact;
        $this->accountManagement = $accountManagement;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Create contact person
     *
     * @param ContactPersonExtendInterface $contactPerson contact person
     * 
     * @return ContactPersonExtendInterface
     */
    public function createContactPerson(ContactPersonExtendInterface $contactPerson)
    {
        $customerId = $contactPerson->getCustomerId();
        $customerData = $this->customerFactory->create()->load($customerId);
        if (! ($customerData->getEntityId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer id doesn't exist", $customerId)
            );
        }
        if ($contactPerson->getCustomAttribute('contactperson_role') == null) {
            throw new \Magento\Framework\Exception\InputException(
                __("[contactperson_role] is a mandatory field")
            );
        } elseif ($contactPerson->getCustomAttribute('contactperson_role')->getValue() == '') {
            throw new \Magento\Framework\Exception\InputException(
                __("You must specify value for [contactperson_role]")
            );
        }
        $contactPersonRole = $contactPerson->getCustomAttribute('contactperson_role')->getValue();
        if ($contactPersonRole != 1 && $contactPersonRole != 2) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Invalid value for [contactperson_role]", $contactPersonRole)
            );
        }
        $return = [];
        $contactPerson->setWebsiteId($customerData->getWebsiteId());
        $contactPerson->setStoreId($customerData->getStoreId());
        $contactPerson->setGroupId($customerData->getGroupId());
        $contactPerson->setCustomAttribute('customer_type', 3);
        if ($contactPerson->getCustomAttribute('customer_status')) {
            $contactPerson
                ->setCustomAttribute(
                    'customer_status', $contactPerson->getCustomAttribute('customer_status')
                        ->getValue()
                );
        }
        $customer = $this->accountManagement->createAccount($contactPerson);
        $contactPerson->setContactPersonId($customer->getId());
        if ($customer->getId()) {
            $contactPersonData = [];
            $contactPersonData['customer_id'] = $customerId;
            $contactPersonData['contactperson_id'] = $customer->getId();
            $contactPersonData['is_active'] = $customer->getCustomAttribute('customer_status')->getValue();
            $contactModel =  $this->contact->create();
            $contactModel->setData($contactPersonData);
            $contactModel->save();
        }
        return $contactPerson;
    }
}
