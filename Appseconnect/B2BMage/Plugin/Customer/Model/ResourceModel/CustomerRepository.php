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

namespace Appseconnect\B2BMage\Plugin\Customer\Model\ResourceModel;

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
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
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
        if ($this->helperContactPerson->isContactPerson($customerObject)) {
            $customerData = $this->helperContactPerson->getCustomerId($customer->getId());
            $extensionAttributes->setCompanyId($customerData);
        }
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currencyCode);
        $currencySymbol = $currency->getCurrencySymbol();
        $extensionAttributes->setCurrencyCode($currencyCode);
        $extensionAttributes->setCurrency($currencySymbol);
        $customer->setExtensionAttributes($extensionAttributes);
        return $customer;
    }
}
