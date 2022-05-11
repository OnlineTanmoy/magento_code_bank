<?php
namespace Appseconnect\CompanyDivision\Plugin\Customer\Block\Address;

use Magento\Framework\Exception\NoSuchEntityException;


/**
 * Class BookPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class BookPlugin
{
    /**
     * Customer repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * CurrentCustomer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    public $currentCustomer;

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
     * Initialize Class variable
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository  CustomerRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomer  $currentCustomer     CurrentCustomer
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data   $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory     CustomerFactory
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {

        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * AroundGetDefaultBilling
     *
     * @param \Magento\Customer\Block\Address\Book $subject Subject
     * @param \Closure                             $proceed Proceed
     *
     * @return NULL|int
     */
    public function aroundGetDefaultBilling(
        \Magento\Customer\Block\Address\Book $subject,
        \Closure $proceed
    ) {

        $customer = $subject->getCustomer();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()
                ->load($customer->getId())
        )
        ) {
            if($this->customerSession->getCurrentCustomerId()) {
                $customer = $this->customerRepository->getById($this->customerSession->getCurrentCustomerId());
            } else {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customer->getId());
                $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
                $customer = $this->customerRepository->getById($customerId);
            }
        }
        if ($customer === null) {
            return null;
        } else {
            return $customer->getDefaultBilling();
        }
    }

    /**
     * AroundGetDefaultShipping
     *
     * @param \Magento\Customer\Block\Address\Book $subject Subject
     * @param \Closure                             $proceed Proceed
     *
     * @return NULL|int
     */
    public function aroundGetDefaultShipping(
        \Magento\Customer\Block\Address\Book $subject,
        \Closure $proceed
    ) {
        $customer = $subject->getCustomer();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()
                ->load($customer->getId())
        )
        ) {
            if($this->customerSession->getCurrentCustomerId()) {
                $customer = $this->customerRepository->getById($this->customerSession->getCurrentCustomerId());
            } else {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customer->getId());
                $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
                $customer = $this->customerRepository->getById($customerId);
            }
        }
        if ($customer === null) {
            return null;
        } else {
            return $customer->getDefaultShipping();
        }
    }

    /**
     * AroundGetAdditionalAddresses
     *
     * @param \Magento\Customer\Block\Address\Book $subject Subject
     * @param \Closure                             $proceed Proceed
     *
     * @return boolean|boolean|array
     */
    public function aroundGetAdditionalAddresses(
        \Magento\Customer\Block\Address\Book $subject,
        \Closure $proceed
    ) {
        $customerId = $this->currentCustomer->getCustomerId();
        try {
            if ($this->helperContactPerson->isContactPerson(
                $this->customerFactory->create()
                    ->load($customerId)
            )
            ) {
                if($this->customerSession->getCurrentCustomerId()) {
                    $customerId = $this->customerSession->getCurrentCustomerId();
                } else {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
                }
            }

            $addresses = $this->customerRepository->getById($customerId)->getAddresses();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
        $primaryAddressIds = [
            $subject->getDefaultBilling(),
            $subject->getDefaultShipping()
        ];
        foreach ($addresses as $address) {
            if (!in_array($address->getId(), $primaryAddressIds)) {
                $additional[] = $address;
            }
        }
        return empty($additional) ? false : $additional;
    }
}
