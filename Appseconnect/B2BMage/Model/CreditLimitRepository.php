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

use Appseconnect\B2BMage\Api\CreditLimit\CreditLimitRepositoryInterface;
use Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;
use Appseconnect\B2BMage\Model\Credit;

/**
 * Class CreditLimitRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreditLimitRepository implements CreditLimitRepositoryInterface
{
    /**
     * Customer resource
     *
     * @var \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    public $customerResourceFactory;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Credit model
     *
     * @var \Appseconnect\B2BMage\Model\CreditFactory
     */
    public $creditModelFactory;
    
    /**
     * Helper contact person
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Helper credit limit
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;
    
    /**
     * Credit limit
     *
     * @var Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface
     */
    public $creditLimit;
    
    /**
     * Extensible data object converter
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * CreditLimitRepository constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data       $helperContactPerson           contact person helper
     * @param \Magento\Customer\Model\CustomerFactory               $customerFactory               customer
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory       customer resource
     * @param CreditFactory                                         $creditModelFactory            credit model
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data         $helperCreditLimit             helper credit limit
     * @param CreditLimitInterface                                  $creditLimit                   credit limit
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter  $extensibleDataObjectConverter extensible data
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Appseconnect\B2BMage\Model\CreditFactory $creditModelFactory,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit,
        CreditLimitInterface $creditLimit,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
    
        $this->customerResourceFactory = $customerResourceFactory;
        $this->customerFactory = $customerFactory;
        $this->creditModelFactory = $creditModelFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->creditLimit = $creditLimit;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Get
     *
     * @param int $customerId customer id
     *
     * @return CreditLimitInterface|Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface
     */
    public function get($customerId)
    {
        $customerData =  $this->customerFactory->create()->load($customerId);
        if (! ($this->customerFactory->create()        ->load($customerId)        ->getEntityId())
        ) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        } else {
            if (! $customerData->getData('firstname') || $customerData->getData('customer_type') != 4) {
                throw new CouldNotSaveException(
                    __("Customer ID is not B2B Customer Type", $customerId)
                );
            } else {
                $creditLimit = $this->customerFactory->create()
                    ->load($customerId)
                    ->getCustomerCreditLimit();
                $availableBalance = $this->customerFactory->create()
                    ->load($customerId)
                    ->getCustomerAvailableBalance();
                if ($creditLimit == '') {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Credit Limit doesn't exist", $customerId)
                    );
                }
                $this->creditLimit->setCustomerId($customerId);
                $this->creditLimit->setCreditLimit($creditLimit);
                $this->creditLimit->setAvailableBalance($availableBalance);
                return $this->creditLimit;
            }
        }
    }

    /**
     * Save
     *
     * @param CreditLimitInterface $creditLimitData credit limit data
     *
     * @return CreditLimitInterface
     */
    public function save(CreditLimitInterface $creditLimitData)
    {
        $customerId = $creditLimitData->getCustomerId();
        $creditLimit = $this->customerFactory->create()
            ->load($customerId)
            ->getCustomerCreditLimit();
        if ($creditLimit != 0) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Credit Limit has been already assigned", $customerId)
            );
        }
        $customerModel = $this->customerFactory->create()->load($customerId);
        $creditLimit = $creditLimitData->getCreditLimit();
        $creditLimitData->setAvailableBalance($creditLimit);
        $creditLimitDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray(
                $creditLimitData,
                [],
                'Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface'
            );
        if ($creditLimit < 1 || $creditLimit > 99999999) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("[credit_limit] must be between 1 and 100000000", $creditLimit)
            );
        } elseif ($creditLimit == '') {
            throw new \Magento\Framework\Exception\InputException(
                __("[credit_limit] is required field")
            );
        }
        if ($customerId == '') {
            throw new \Magento\Framework\Exception\InputException(
                __("[customer_id] is a required field")
            );
        } elseif (! ($customerModel->getEntityId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        } elseif (! $this->helperContactPerson->isB2Bcustomer($customerId)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID is not B2B Customer Type", $customerId)
            );
        }
        $customer = $this->customerFactory->create();
        $customerData = $customer->getDataModel();
        $customerData->setId($customerId);
        $customerData->setCustomAttribute('customer_credit_limit', $creditLimit);
        $customerData->setCustomAttribute('customer_available_balance', $creditLimit);
        $customer->updateData($customerData);
        $customerResource = $this->customerResourceFactory->create();
        $customerResource->saveAttribute($customer, 'customer_credit_limit');
        $customerResource->saveAttribute($customer, 'customer_available_balance');
        $creditLimitDataArray['credit_amount'] = 0;
        $creditModel = $this->creditModelFactory->create();
        $creditModel->setData($creditLimitDataArray);
        $creditModel->save();
        return $creditLimitData;
    }

    /**
     * Update
     *
     * @param CreditLimitInterface $creditLimitData creditlimit data
     *
     * @return CreditLimitInterface
     */
    public function update(CreditLimitInterface $creditLimitData)
    {
        $customerId = $creditLimitData->getCustomerId();
        $customerModel = $this->customerFactory->create()->load($customerId);
        $creditLimit = $creditLimitData->getCreditLimit();
        if ($customerId == '') {
            throw new \Magento\Framework\Exception\InputException(__("[customer_id] is a required field"));
        } elseif (! ($customerModel->getEntityId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        } elseif (! $this->helperContactPerson->isB2Bcustomer($customerId)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID is not B2B Customer Type", $customerId)
            );
        }
        if ($creditLimit == '') {
              $creditLimit = 0;
              $creditLimitData->setCreditLimit(0);
        } elseif ($creditLimit < 1 || $creditLimit > 99999999) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("[credit_limit] must be between 1 and 100000000", $creditLimit)
            );
        }
          $debitAmount = $this->helperCreditLimit->getCustomerDebitAmount($customerId);
          $lastTransactionData = $this->helperCreditLimit->getCustomerCreditData($customerId);
        if ($creditLimit == $lastTransactionData['credit_limit']) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Credit limit already provided.Kindly update.", $creditLimit)
            );
        }
          $lastAvailableBalance = $lastTransactionData['available_balance'];
          $lastCreditLimit = $lastTransactionData['credit_limit'];
          $updatedAvailableBalance = $lastAvailableBalance + ($creditLimit - $lastCreditLimit);
          $creditLimitData->setAvailableBalance($updatedAvailableBalance);
          $creditLimitDataArray = $this->extensibleDataObjectConverter
              ->toNestedArray($creditLimitData, [], 'Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface');
          $creditModel = $this->creditModelFactory->create();
          $creditModel->setData($creditLimitDataArray);
          $creditModel->save();
          $customer = $this->customerFactory->create();
          $customerData = $customer->getDataModel();
          $customerData->setId($customerId);
          $customerData->setCustomAttribute('customer_credit_limit', $creditLimit);
          $customerData->setCustomAttribute('customer_available_balance', $updatedAvailableBalance);
          $customer->updateData($customerData);
          $customerResource = $this->customerResourceFactory->create();
          $customerResource->saveAttribute($customer, 'customer_credit_limit');
          $customerResource->saveAttribute($customer, 'customer_available_balance');
          return $creditLimitData;
    }

    /**
     * Save customer credit
     *
     * @param $customerCreditDetail customer credit details
     * @param $creditLimitDataArray credit limit data array
     * @param $customerCreditLimit  customer credit limiy
     *
     * @return void
     */
    public function saveCustomerCredit($customerCreditDetail, $creditLimitDataArray, $customerCreditLimit)
    {
        if (empty($customerCreditDetail) && $customerCreditLimit > 0) {
            $creditLimitDataArray['available_balance'] = $customerCreditLimit;
            $creditLimitDataArray['credit_limit'] = $customerCreditLimit;
            $creditModel = $this->creditModelFactory->create();
            $creditModel->setData($creditLimitDataArray)->save();
        } elseif ($customerCreditDetail) {
            $availableBalance = $customerCreditDetail['available_balance'];
            $creditLimit = $customerCreditDetail['credit_limit'];
            if ($creditLimit != $customerCreditLimit && $availableBalance == $creditLimit) {
                $availableBalance = $availableBalance + ($customerCreditLimit - $creditLimit);
                $creditLimitDataArray['available_balance'] = $availableBalance;
                $creditLimitDataArray['credit_limit'] = $customerCreditLimit;
                $creditModel = $this->creditModelFactory->create();
                $creditModel->setData($creditLimitDataArray)->save();
            }
        }
    }
}
