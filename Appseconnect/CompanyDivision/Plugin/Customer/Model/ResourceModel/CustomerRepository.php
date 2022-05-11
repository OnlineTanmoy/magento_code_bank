<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\CompanyDivision\Plugin\Customer\Model\ResourceModel;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;

/**
 * Class CustomerRepository
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerRepository
{

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;


    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * CurrencyFactory
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     * CustomerRepository constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager        StoreManager
     * @param \Magento\Directory\Model\CurrencyFactory        $currencyFactory     CurrencyFactory
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        CustomerRegistry $customerRegistry
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->divisionHelper = $divisionHelper;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * AfterGetById
     *
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $subject  Subject
     * @param $customer Customer
     *
     * @return mixed
     */
    public function afterGetById(\Magento\Customer\Model\ResourceModel\CustomerRepository $subject, $customer)
    {
        $customerObject = $this->helperContactPerson->customerFactory->create()->load($customer->getId());
        $extensionAttributes = $customer->getExtensionAttributes();
        if ($customerObject->getCustomerType() == 4 && !$this->divisionHelper->isMainCustomer($customer->getId())) {
            $customerData = $this->divisionHelper->getParentCustomerId($customer->getId());
            $extensionAttributes->setParentCompanyId($customerData);
        }
        $customer->setExtensionAttributes($extensionAttributes);
        return $customer;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $subject
     * @param $customer
     * @return mixed
     */
    public function afterGet(\Magento\Customer\Model\ResourceModel\CustomerRepository $subject, $customer)
    {
        $customerObject = $this->helperContactPerson->customerFactory->create()->load($customer->getId());
        $extensionAttributes = $customer->getExtensionAttributes();
        $this->divisionHelper->isMainCustomer($customer->getId());
        if ($customerObject->getCustomerType() == 4 && !$this->divisionHelper->isMainCustomer($customer->getId())) {
            $customerData = $this->divisionHelper->getParentCustomerId($customer->getId());
            $extensionAttributes->setParentCompanyId($customerData);
        }
        $customer->setExtensionAttributes($extensionAttributes);
        return $customer;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $subject
     * @param callable $proceed
     * @param CustomerInterface $customer
     * @param null $passwordHash
     * @return mixed
     */
    public function aroundSave(\Magento\Customer\Model\ResourceModel\CustomerRepository $subject, callable $proceed, CustomerInterface $customer, $passwordHash = null)
    {
        $savedCustomer = $proceed($customer, $passwordHash);
        $customerObject = $this->helperContactPerson->customerFactory->create()->load($savedCustomer->getId());

        $customerArray = $customer->__toArray();
        if (isset($customerArray['extension_attributes']['parent_company_id']) && !$this->divisionHelper->getParentCustomerId($customer->getId())) {
            $divisionId = $this->divisionHelper->createDivision($customerObject, $customerArray['extension_attributes']['parent_company_id']);
            if($divisionId) {
                $savedCustomer = $subject->get($customer->getEmail(), $customer->getWebsiteId());
            }
        }

        return $savedCustomer;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $subject
     * @param $customerId
     * @return array
     */
    public function beforeDeleteById(\Magento\Customer\Model\ResourceModel\CustomerRepository $subject, $customerId)
    {
        if($customerId) {
            $divisionList = $this->divisionHelper->divisionFactory->create()->getCollection()
                ->AddFieldToFilter('division_id', $customerId);
            foreach($divisionList as $division) {
                $division->delete();
            }

            $customerList = $this->divisionHelper->divisionFactory->create()->getCollection()
                ->AddFieldToFilter('customer_id', $customerId);
            foreach($customerList as $customerDiv) {
                $customerDiv->delete();
                $customerModel = $this->customerRegistry->retrieve($customerDiv->getDivisionId());
                $customerModel->delete();
                $this->customerRegistry->remove($customerDiv->getDivisionId());
            }
        }

        return [$customerId];
    }
}
