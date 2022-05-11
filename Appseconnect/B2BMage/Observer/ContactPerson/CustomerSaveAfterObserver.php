<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\ContactPerson;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerSaveAfterObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerSaveAfterObserver implements ObserverInterface
{

    /**
     * CreditLimitRepository
     *
     * @var \Appseconnect\B2BMage\Model\CreditLimitRepository
     */
    public $creditLimitRepository;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Indexer
     *
     * @var \Magento\Indexer\Model\Indexer
     */
    public $indexer;

    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * Http
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    public $customerResource;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * CustomerSaveAfterObserver constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResource      CustomerResource
     * @param \Appseconnect\B2BMage\Model\CreditLimitRepository     $creditLimitRepository CreditLimitRepository
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data         $helperCreditLimit     HelperCreditLimit
     * @param \Magento\Customer\Model\CustomerFactory               $customerFactory       CustomerFactory
     * @param \Magento\Framework\Message\ManagerInterface           $messageManager        MessageManager
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data       $helperContactPerson   HelperContactPerson
     * @param \Magento\Indexer\Model\IndexerFactory                 $indexer               Indexer
     * @param Http                                                  $request               Request
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResource,
        \Appseconnect\B2BMage\Model\CreditLimitRepository $creditLimitRepository,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Indexer\Model\IndexerFactory $indexer,
        Http $request
    ) {
        $this->request = $request;
        $this->creditLimitRepository = $creditLimitRepository;
        $this->customerResource = $customerResource;
        $this->customerFactory = $customerFactory;
        $this->messageManager = $messageManager;
        $this->indexer = $indexer;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->helperContactPerson = $helperContactPerson;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $indexer = $this->indexer->create();
        $indexer->load('catalogrule_rule');
        $indexer->reindexAll();
        $customerData = $observer->getEvent()->getData('customer');
        $customerWebsiteId = $customerData->getWebsiteId();
        $customerGroupId = $customerData->getGroupId();
        $customerId = $customerData->getId();

        $getParams = $this->request->getParams();

        $isB2Bcustomer = $this->helperContactPerson->isB2Bcustomer($customerId);

        $customer = $this->customerFactory->create();
        $customerCollection = $customer->getDataModel();
        $customerCollection->setId($customerId);
        if ($isB2Bcustomer) {
            $customerCreditDetail = $this->helperCreditLimit->getCustomerCreditData($customerId);

            $check = true;
            $customerCreditLimit = $getParams['customer']['customer_credit_limit'];

            $warningMessage = $this->helperContactPerson->validateCreditLimit(
                $customerCreditLimit,
                $customerCreditDetail,
                $this->customerFactory->create()
                    ->load($customerId)
            );

            if ($warningMessage) {
                $check = false;
                $this->messageManager->addWarning($warningMessage);
            }
            $availableBalance = null;

            if (!empty($customerCreditLimit)
                && $isB2Bcustomer
                && is_numeric($customerCreditLimit)
                && $customerCreditLimit > 99999999.9999
            ) {
                $this->messageManager->addWarning('Credit limit must be between 1 and 100000000.');
                $customerCreditLimit = ($customerCreditDetail) ? $customerCreditDetail['credit_limit'] : 10000000.0000;
            }
            $customerCreditLimit = ($customerCreditLimit == "") ? 0 : $customerCreditLimit;
            $customerCreditLimit = number_format($customerCreditLimit, 2, '.', '');

            $creditLimitDataArray = [];
            $creditLimitDataArray['customer_id'] = $customerId;
            $availableBalance = (isset($customerCreditDetail['available_balance'])) ?
                number_format($customerCreditDetail['available_balance'], 2, '.', '') :
                $customerCreditLimit;

            $this->creditLimitRepository->saveCustomerCredit(
                $customerCreditDetail,
                $creditLimitDataArray,
                $customerCreditLimit
            );
            if ($customerCreditDetail
                && $customerCreditDetail['credit_limit'] != $customerCreditLimit
                && $customerCreditDetail['available_balance'] != $customerCreditDetail['credit_limit']
            ) {
                $customerCreditLimit = $customerCreditDetail['credit_limit'];
                if ($check) {
                    $this->messageManager->addWarning(
                        'Credit limit can only be changed if available balance and credit limit are same.'
                    );
                }
            }
            $customerCollection->setCustomAttribute('customer_credit_limit', $customerCreditLimit);
            $customerCollection->setCustomAttribute('customer_available_balance', $availableBalance);

            $contactPersonData = $this->helperContactPerson->getContactPersonId($customerId);
            // to change the contactperson website as on customer
            if ($contactPersonData) {
                foreach ($contactPersonData as $val) {
                    $this->processContactPerson($val, $customerGroupId, $customerWebsiteId);
                }
            }
            // end
        } else {
            $customerCollection->setCustomAttribute('customer_available_balance', null);
            $customerCreditLimit = $customerCollection->getCustomAttribute('customer_credit_limit');
            if ($customerCreditLimit) {
                $customerCollection->setCustomAttribute('customer_credit_limit', null);
                $this->messageManager->addError('Credit limit is only for B2B customer.');
            }
            $pricelistCode = $customerCollection->getCustomAttribute('pricelist_code');
            if ($pricelistCode) {
                $customerCollection->setCustomAttribute('pricelist_code', null);
                $this->messageManager->addError('Pricelist is only for B2B customer.');
            }
        }
        $customer->updateData($customerCollection);
        $customerResource = $this->customerResource->create();
        $customerResource->saveAttribute($customer, 'customer_credit_limit');
        $customerResource->saveAttribute($customer, 'customer_available_balance');
    }

    /**
     * ProcessContactPerson
     *
     * @param array $data              Data
     * @param int   $customerGroupId   CustomerGroupId
     * @param int   $customerWebsiteId CustomerWebsiteId
     *
     * @return void
     */
    public function processContactPerson($data, $customerGroupId, $customerWebsiteId)
    {
        $contactPersonCollection = $this->customerFactory->create()->load($data['contactperson_id']);
        $contactPersonCollection->setGroupId($customerGroupId);
        $contactPersonCollection->setWebsiteId($customerWebsiteId);
        $contactPersonCollection->save();
    }
}
