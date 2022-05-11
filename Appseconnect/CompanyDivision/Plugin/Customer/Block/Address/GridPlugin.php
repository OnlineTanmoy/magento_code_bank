<?php


namespace Appseconnect\CompanyDivision\Plugin\Customer\Block\Address;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\Session;

class GridPlugin
{
    public function __construct(
        CustomerRepository $customerRepository,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {

        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }


    public function afterGetCustomer(\Magento\Customer\Block\Address\Grid $subject, $result)
    {
        $customer = $result;
        if ($this->helperContactPerson->isContactPerson($this->customerSession->getCustomer())) {
            if ($this->customerSession->getCurrentCustomerId()) {
                $customerId = $this->customerSession->getCurrentCustomerId();
                $customer = $this->customerRepository->getById($customerId);
                $subject->setData('customer', $customer);
                return $customer;

            } else {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($this->customerSession->getCustomer()->getId());
                $customerId = $parentCustomerMapData['customer_id'];
                $customer = $this->customerRepository->getById($customerId);
                $subject->setData('customer', $customer);
                return $customer;
            }
        } else {
            return $customer;
        }

    }
}
