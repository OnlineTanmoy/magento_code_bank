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
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class QuoteAddressValidatorPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteAddressValidatorPlugin
{

    /**
     * Address factory.
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * Customer repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * Session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

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
     * @param \Magento\Customer\Api\AddressRepositoryInterface  $addressRepository   AddressRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository  CustomerRepository
     * @param Session                                           $customerSession     CustomerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data   $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory     CustomerFactory
     */
    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Validates the fields in a specified address data object.
     *
     * @param \Magento\Quote\Model\QuoteAddressValidator $subject     Subject
     * @param \Closure                                   $proceed     Proceed
     * @param AddressInterface                           $addressData AddressData
     *
     * @return bool
     */
    public function aroundValidate(
        \Magento\Quote\Model\QuoteAddressValidator $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\AddressInterface $addressData
    ) {
        if ($addressData->getCustomerId()) {
            $customer = $this->customerRepository->getById($addressData->getCustomerId());
            if (!$customer->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Invalid customer id %1', $addressData->getCustomerId())
                );
            }
        }

        if ($addressData->getCustomerAddressId()) {
            try {
                $this->addressRepository->getById($addressData->getCustomerAddressId());
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Invalid address id %1', $addressData->getId())
                );
            }

            $customerId = $addressData->getCustomerId();

            $customer = $this->customerFactory->create()->load($customerId);
            if ($this->helperContactPerson->isContactPerson($customer)) {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                $customerId = $parentCustomerMapData ?
                    $parentCustomerMapData['customer_id'] : $customerId;
            }

            $applicableAddressIds = array_map(
                function ($addressData) {
                    return $addressData->getId();
                }, $this->customerRepository->getById($customerId)->getAddresses()
            );
            if (!in_array($addressData->getCustomerAddressId(), $applicableAddressIds)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Invalid customer address id %1', $addressData->getCustomerAddressId())
                );
            }
        }
        return true;
    }

    /**
     * AroundValidateForCart
     *
     * @param \Magento\Quote\Model\QuoteAddressValidator $subject Subject
     * @param \Closure                                   $proceed Proceed
     * @param CartInterface                              $cart    Cart
     * @param AddressInterface                           $address Address
     *
     * @return bool | mixed
     */
    public function aroundValidateForCart(
        \Magento\Quote\Model\QuoteAddressValidator $subject,
        \Closure $proceed,
        CartInterface $cart,
        AddressInterface $address
    ) {
        $result = true;
        $customerId = $cart->getCustomerIsGuest() ? null : $cart->getCustomer()->getId();
        if ($customerId) {
            $customer = $this->customerFactory->create()->load($customerId);
            if ($this->helperContactPerson->isContactPerson($customer)) {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
                if (isset($parentCustomerMapData['customer_id'])) {
                    return true;
                }
            }
        }
        $result = $proceed($cart, $address);
        return $result;
    }
}
