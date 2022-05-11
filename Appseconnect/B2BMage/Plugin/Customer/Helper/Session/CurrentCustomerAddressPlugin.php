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

namespace Appseconnect\B2BMage\Plugin\Customer\Helper\Session;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class CurrentCustomerAddressPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CurrentCustomerAddressPlugin
{

    /**
     * CurrentCustomer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    public $currentCustomer;

    /**
     * AccountManagementInterface
     *
     * @var AccountManagementInterface
     */
    public $accountManagement;

    /**
     * Helper Contact Person.
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Initialize class variable
     *
     * @param CurrentCustomer                                 $currentCustomer     CurrentCustomer
     * @param AccountManagementInterface                      $accountManagement   AccountManagement
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     CustomerFactory
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        AccountManagementInterface $accountManagement,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {

        $this->currentCustomer = $currentCustomer;
        $this->accountManagement = $accountManagement;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }

    /**
     * AroundGetDefaultBillingAddress
     *
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress $subject Subject
     * @param \Closure                                                $proceed Proceed
     *
     * @return \Magento\Framework\View\Result\Page|mixed
     */
    public function aroundGetDefaultBillingAddress(
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $subject,
        \Closure $proceed
    ) {

        $customerId = $this->currentCustomer->getCustomerId();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()
                ->load($customerId)
        )
        ) {
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
        }
        return $this->accountManagement->getDefaultBillingAddress($customerId);
    }

    /**
     * AroundGetDefaultShippingAddress
     *
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress $subject Subject
     * @param \Closure                                                $proceed Proceed
     *
     * @return \Magento\Framework\View\Result\Page|mixed
     */
    public function aroundGetDefaultShippingAddress(
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $subject,
        \Closure $proceed
    ) {

        $customerId = $this->currentCustomer->getCustomerId();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()
                ->load($customerId)
        )
        ) {
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
        }
        return $this->accountManagement->getDefaultShippingAddress($customerId);
    }
}
