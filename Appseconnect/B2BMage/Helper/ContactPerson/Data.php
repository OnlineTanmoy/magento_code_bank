<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\ContactPerson;

use Appseconnect\B2BMage\Model\ResourceModel\Contactpersonaddress\CollectionFactory;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    const TYPE_CONTACT_PERSON = 3;
    
    const TYPE_SALES_REP = 2;
    
    const TYPE_B2B = 4;
    
    const TYPE_B2C = 1;
    
    /**
     * ContactFactory
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactPersonFactory;
    
    /**
     * CollectionFactory
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Contactpersonaddress\CollectionFactory
     */
    public $contactPersonAddressCollectionFactory;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * HttpContext
     * 
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context      $context                               Context
     * @param \Appseconnect\B2BMage\Model\ContactFactory $contactPersonFactory                  ContactPersonFactory
     * @param CollectionFactory                          $contactPersonAddressCollectionFactory ContactPersonAddressCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory                       CustomerFactory
     * @param \Magento\Framework\App\Http\Context        $httpContext                           HttpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Appseconnect\B2BMage\Model\ContactFactory $contactPersonFactory,
        CollectionFactory $contactPersonAddressCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->contactPersonFactory = $contactPersonFactory;
        $this->contactPersonAddressCollectionFactory = $contactPersonAddressCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * IsAdministrator
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return number|int
     */
    public function isAdministrator($contactPersonId)
    {
        $customerCollection = $this->customerFactory->create()->load($contactPersonId);
        $role = $customerCollection->getContactpersonRole();
        if ($customerCollection->getData('customer_type') != 3) {
            return 0;
        }
        return $role;
    }

    /**
     * getCustomerIdForMinimal
     *
     * @return mixed
     */
    public function getCustomerIdForMinimal()
    {
        return $this->httpContext->getValue('customer_id');
    }

    /**
     * CheckCustomerStatus
     *
     * @param int     $customerId CustomerId
     * @param boolean $allData    AllData
     *
     * @return array
     */
    public function checkCustomerStatus($customerId, $allData = null)
    {
        $customerCollection = $this->customerFactory->create()->load($customerId);
        $customerData = $customerCollection->getData('customer_status');
        if ($allData) {
            $customerData = $customerCollection->getData();
        }
        return $customerData;
    }

    /**
     * IsAdmin
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return boolean
     */
    public function isAdmin($contactPersonId)
    {
        return true;
    }

    /**
     * GetCustomerData
     *
     * @param int $customerId CustomerId
     *
     * @return array
     */
    public function getCustomerData($customerId)
    {
        $customerCollection = $this->customerFactory->create()
            ->load($customerId)
            ->getData();
        return $customerCollection;
    }

    /**
     * GetCustomerDataByContactPersonId
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return array
     */
    public function getCustomerDataByContactPersonId($contactPersonId)
    {
        $customerId = $this->getCustomerId($contactPersonId);
        $customerCollection = $this->customerFactory->create()
            ->load($customerId)
            ->getData();
        return $customerCollection;
    }

    /**
     * GetCustomerNameById
     *
     * @param int $id Id
     *
     * @return string
     */
    public function getCustomerNameById($id)
    {
        return $this->customerFactory->create()
            ->load($id)
            ->getName();
    }

    /**
     * GetParentCustomerName
     *
     * @param \Magento\Customer\Model\Customer $customer Customer
     *
     * @return string
     */
    public function getParentCustomerName($customer)
    {
        $parentCustomerName = null;
        if ($customer->getCustomAttribute('customer_type')->getValue() == 3) {
            $parentCustomerMapData = $this->getCustomerId($customer->getId());
            if (isset($parentCustomerMapData['customer_id']) 
                && $parentCustomerMapData['customer_id']
            ) {
                $parentCustomerId = $parentCustomerMapData['customer_id'];
                $parentCustomerName = $this->getCustomerNameById($parentCustomerId);
            }
        }
        return $parentCustomerName;
    }

    /**
     * IsValidCustomer
     *
     * @param int $customerId CustomerId
     *
     * @return string|boolean
     */
    public function isValidCustomer($customerId)
    {
        $check = true;
        $contactPersonId = null;
        
        $customerCollection = $this->customerFactory->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $customerId);
        
        $customerCollectionData = $customerCollection->getData();
        
        if ($customerCollectionData && isset($customerCollectionData[0])) {
            $customerStatus = $customerCollectionData[0]['customer_status'];
            $customerType = $customerCollectionData[0]['customer_type'];
            $contactPersonId = $customerCollectionData[0]['entity_id'];
        }
        
        if ($contactPersonId && $customerStatus) {
            if ($customerType == 4) {
                return 'B2BCustomer';
            } elseif ($customerType == 3) {
                $b2bCustomerId = $this->getContactCustomerId($contactPersonId);
                $b2bCustomerStatus = $this->customerFactory->create()->load($b2bCustomerId)
                    ->getCustomerStatus();
                if (! $b2bCustomerStatus) {
                    return 'inactive';
                }
            } elseif ($customerType == 2) {
                return 'salesrep';
            }
        } elseif (! $customerStatus && $customerType == 3) {
            return 'customerInactive';
        } elseif (! $customerStatus && $customerType == 2) {
            return 'salesrepInactive';
        } else {
            return 'inactive';
        }
        
        return $check;
    }

    /**
     * GetCustomerId
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return array
     */
    public function getCustomerId($contactPersonId)
    {
        $contactPersonCollection = $this->contactPersonFactory->create()->getCollection();
        $contactPersonCollection->addFieldToFilter('contactperson_id', $contactPersonId);
        $contactPersonData = $contactPersonCollection->getData();
        if ($contactPersonData && isset($contactPersonData[0])) {
            return $contactPersonData[0];
        }
        return null;
    }

    /**
     * GetContactPersonId
     *
     * @param int $customerId CustomerId
     *
     * @return array
     */
    public function getContactPersonId($customerId)
    {
        $contactPersonCollection = $this->contactPersonFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $contactPersonCollection->addFieldToSelect(
            [
            'contactperson_id',
            'id'
            ]
        );
        return $contactPersonCollection->getData();
    }

    /**
     * IsB2Bcustomer
     *
     * @param int $customerId CustomerId
     *
     * @return boolean
     */
    public function isB2Bcustomer($customerId)
    {
        $check = true;
        $customerType = null;
        $customerCollection = $this->customerFactory->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $customerId);
        
        $customerCollectionData = $customerCollection->getData();
        
        if (isset($customerCollectionData[0])) {
            $customerType = $customerCollectionData[0]['customer_type'];
        }
        if ($customerType == 4) {
            return true;
        }
        return false;
    }

    /**
     * ContactPersonExist
     *
     * @param int $customerId CustomerId
     *
     * @return boolean
     */
    public function contactPersonExist($customerId)
    {
        $contactPersonCollection = $this->contactPersonFactory->create()->getCollection();
        $contactPersonCollection->addFieldToFilter('customer_id', $customerId);
        $result = $contactPersonCollection->getData() ? true : false;
        return $result;
    }

    /**
     * ValidateCreditLimit
     *
     * @param array                            $customerCreditLimit  CustomerCreditLimit
     * @param array                            $customerCreditDetail CustomerCreditDetail
     * @param \Magento\Customer\Model\Customer $customerCollection   CustomerCollection
     *
     * @return NULL|string
     */
    public function validateCreditLimit(
        $customerCreditLimit,
        $customerCreditDetail,
        $customerCollection
    ) {
    
        $message = null;
        if (! empty($customerCreditLimit) 
            && (! is_numeric($customerCreditLimit) 
            || $customerCreditLimit < 0)
        ) {
            $message = 'Credit limit can only be positive and numeric';
            $customerCreditLimit = ($customerCreditDetail) ? $customerCreditDetail['credit_limit'] : 0;
        } elseif (! empty($customerCreditLimit) 
            && is_numeric($customerCreditLimit) 
            && $customerCreditLimit < 1 
            && $customerCreditLimit != 0
        ) {
            $message = 'Credit must be greater than equal to 1';
            $customerCreditLimit = ($customerCreditDetail) ? $customerCreditDetail['credit_limit'] : 0;
        } elseif (($customerCreditLimit == 0 
            || $customerCreditLimit == '') 
            && (!isset($customerCreditDetail['credit_limit']))
        ) {
            $message = 'No credit limit applied';
            $customerCreditLimit = 0;
        } elseif (($customerCreditLimit == 0
            || $customerCreditLimit == '')
            &&  $customerCreditDetail['credit_limit'] == $customerCollection->getCustomerAvailableBalance()
        ) {
            $message = 'No credit limit applied';
            $customerCreditLimit = 0;
        } elseif ($customerCreditLimit == ''
            && $customerCreditDetail['credit_limit'] != $customerCollection->getCustomerAvailableBalance()
        ) {
            $message = 'Credit limit can only be changed if available balance and credit limit are same';
            $customerCreditLimit = $customerCreditDetail['credit_limit'];
        }
        
        return $message;
    }
    
    /**
     * GetContactCustomerId
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return int
     */
    public function getContactCustomerId($contactPersonId)
    {
        $contactPersonCollection = $this->contactPersonFactory->create()->getCollection();
        $contactPersonCollection->addFieldToFilter('contactperson_id', $contactPersonId);
        $contactPersonData = $contactPersonCollection->getData();
        if ($contactPersonData && isset($contactPersonData[0]['customer_id'])) {
            return $contactPersonData[0]['customer_id'];
        }
        return null;
    }
    
    /**
     * IsContactPerson
     *
     * @param \Magento\Customer\Model\Customer $customer Customer
     *
     * @return boolean
     */
    public function isContactPerson($customer)
    {
        return $customer->getCustomerType() == self::TYPE_CONTACT_PERSON ? true : false;
    }
}
