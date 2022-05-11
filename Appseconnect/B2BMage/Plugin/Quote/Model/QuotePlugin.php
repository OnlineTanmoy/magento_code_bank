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

namespace Appseconnect\B2BMage\Plugin\Quote\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class QuotePlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuotePlugin
{

    /**
     * Customer repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Helper Contact Person.
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    protected $helperContactPerson;

    /**
     * Constructs a quote shipping address validator service object.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository  CustomerRepository
     * @param Session                                           $customerSession     CustomerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data   $helperContactPerson HelperContactPerson
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
    }

    /**
     * AroundAddCustomerAddress
     *
     * @param \Magento\Quote\Model\Quote                  $subject Subject
     * @param \Closure                                    $proceed Proceed
     * @param \Magento\Customer\Api\Data\AddressInterface $address Address
     *
     * @return mixed
     */
    public function aroundAddCustomerAddress(
        \Magento\Quote\Model\Quote $subject,
        \Closure $proceed,
        \Magento\Customer\Api\Data\AddressInterface $address
    ) {
        $customerId = $subject->getCustomer()->getId();
        if ($this->helperContactPerson->isContactPerson($this->customerSession->getCustomer())) {
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
            $customer = $this->customerRepository->getById($customerId);
            $addresses = $customer->getAddresses();
            $address->setIsDefaultShipping(false);
            $address->setIsDefaultBilling(false);
            $addresses = (array)$addresses;
            $addresses[] = $address;
            $customer->setAddresses($addresses);
            $this->customerRepository->save($customer);
        } else {
            $result = $proceed($address);
            return $result;
        }
    }
}
