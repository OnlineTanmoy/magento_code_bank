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
namespace Appseconnect\B2BMage\Helper\CreditLimit;

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
    
    public $creditModel;
    
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    public $customerResource;
    
    /**
     * @var \Appseconnect\B2BMage\Model\CreditFactory
     */
    public $creditModelFactory;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory               $customerFactory    CustomerFactory
     * @param \Appseconnect\B2BMage\Model\CreditFactory             $creditModelFactory CreditModelFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResource   CustomerResource
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\CreditFactory $creditModelFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResource
    ) {
            $this->customerFactory = $customerFactory;
            $this->creditModelFactory = $creditModelFactory;
            $this->customerResource = $customerResource;
    }
    
    /**
     * CreditFactory
     *
     * @return \Appseconnect\B2BMage\Model\CreditFactory
     */
    public function getCreditCollection()
    {
        return $this->creditModelFactory->create()->getCollection();
    }
    
    /**
     * GetCustomerUpdatedCreditBalance
     *
     * @param int   $customerId          CustomerId
     * @param float $updatedCreditAmount UpdatedCreditAmount
     *
     * @return number
     */
    public function getCustomerUpdatedCreditBalance($customerId, $updatedCreditAmount)
    {
        $creditCollection = $this->creditModelFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->setPageSize(1)
            ->setOrder('id', 'DESC')
            ->setCurPage(1);
        $data = $creditCollection->getData();
        if (! empty($data) && isset($data[0])) {
            return $updatedCreditAmount - $data[0]['debit_amount'];
        }
        return 0;
    }
    
    /**
     * GetCustomerCreditData
     *
     * @param int $customerId CustomerId
     *
     * @return array|number
     */
    public function getCustomerCreditData($customerId)
    {
        $creditCollection = $this->creditModelFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->setPageSize(1)
            ->setOrder('id', 'DESC')
            ->setCurPage(1);
        $creditCollection->addFieldToSelect(
            [
            'available_balance',
            'credit_limit'
            ]
        );
        $data = $creditCollection->getData();
        if (! empty($data) && isset($data[0])) {
            return $data[0];
        }
        return 0;
    }
    
    /**
     * GetCustomerCreditAmount
     *
     * @param int $customerId CustomerId
     *
     * @return array|number
     */
    public function getCustomerCreditAmount($customerId)
    {
        $creditCollection = $this->creditModelFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->setPageSize(1)
            ->setOrder('id', 'DESC')
            ->setCurPage(1);
        $data = $creditCollection->getData();
        
        if (! empty($data) && isset($data[0])) {
            return $data[0]['credit_amount'];
        }
        return 0;
    }
    
    /**
     * GetCustomerDebitAmount
     *
     * @param int $customerId CustomerId
     *
     * @return array|number
     */
    public function getCustomerDebitAmount($customerId)
    {
        $creditCollection = $this->creditModelFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->setPageSize(1)
            ->setOrder('id', 'DESC')
            ->setCurPage(1);
        $data = $creditCollection->getData();
        if (! empty($data) && isset($data[0])) {
            return $data[0]['debit_amount'];
        }
        return 0;
    }
    
    /**
     * IsValidPayment
     *
     * @param array $paymentMathode PaymentMathode
     *
     * @return boolean
     */
    public function isValidPayment($paymentMathode)
    {
        $check = false;
        $paymentMathodeApplyed = [
            'creditlimit'
        ];
        if (in_array($paymentMathode, $paymentMathodeApplyed)) {
            $check = true;
        }
        return $check;
    }
    
    /**
     * CreditLimitUpdate
     *
     * @param int                        $customerId      CustomerId
     * @param \Magento\Sales\Model\Order $orderCollection OrderCollection
     * @param float                      $grandTotal      GrandTotal
     * @param float                      $cancelAmount    CancelAmount
     *
     * @return void
     */
    public function creditLimitUpdate($customerId, $orderCollection, $grandTotal, $cancelAmount = null)
    {
        if ($cancelAmount > 0) {
            $grandTotal = $cancelAmount;
        }
        
        $incrementId = $orderCollection->getIncrementId();
        
        $creditCollection = $this->getCreditCollection()->getCreditBalance($incrementId);
        $creditBalance = $creditCollection->getData();
        if (! empty($creditBalance) && isset($creditBalance[0]['dif']) && $creditBalance[0]['dif'] > 0) {
            $customerCreditDetail = $this->getCustomerCreditData($customerId);
            $creditLimit = $customerCreditDetail['credit_limit'];
            $availableBalance = $customerCreditDetail['available_balance'];
            $creditLimitDataArray = [];
            $creditLimitDataArray['customer_id'] = $customerId;
            $creditLimitDataArray['credit_limit'] = $creditLimit;
            $creditLimitDataArray['increment_id'] = $incrementId;
            $creditLimitDataArray['available_balance'] = $availableBalance + $grandTotal;
            $creditLimitDataArray['credit_amount'] = $grandTotal;
            $creditModel = $this->creditModelFactory->create();
            $creditModel->setData($creditLimitDataArray);
            $creditModel->save();
            
            $customerCollection = $this->customerFactory->create()->load($customerId);
            $this->saveCreditLimit($customerId, $creditLimit);
            $this->saveCreditBalance($customerId, $creditLimitDataArray['available_balance']);
        }
    }
    
    /**
     * SaveCreditLimit
     *
     * @param int   $customerId  CustomerId
     * @param float $creditLimit CreditLimit
     *
     * @return void
     */
    public function saveCreditLimit($customerId, $creditLimit)
    {
        $customer = $this->customerFactory->create();
        $customerCollection = $customer->getDataModel();
        $customerCollection->setId($customerId);
        $customerCollection->setCustomAttribute(
            'customer_credit_limit',
            number_format($creditLimit, 2, '.', '')
        );
        $customer->updateData($customerCollection);
        $customerResource = $this->customerResource->create();
        $customerResource->saveAttribute($customer, 'customer_credit_limit');
    }
    
    /**
     * SaveCreditBalance
     *
     * @param int   $customerId CustomerId
     * @param float $balance    Balance
     *
     * @return void
     */
    public function saveCreditBalance($customerId, $balance)
    {
        $customer = $this->customerFactory->create();
        $customerCollection = $customer->getDataModel();
        $customerCollection->setId($customerId);
        $customerCollection->setCustomAttribute(
            'customer_available_balance',
            number_format($balance, 2, '.', '')
        );
        $customer->updateData($customerCollection);
        $customerResource = $this->customerResource->create();
        $customerResource->saveAttribute($customer, 'customer_available_balance');
    }
}
