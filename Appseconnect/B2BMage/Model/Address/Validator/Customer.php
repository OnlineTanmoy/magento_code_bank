<?php

declare(strict_types=1);
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Address\Validator;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;

/**
 * Model Customer
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customer extends \Magento\Customer\Model\Address\Validator\Customer
{
    /**
     * Address factory
     *
     * @var AddressFactory
     */
    private $_addressFactory;

    /**
     * Customer constructor.
     *
     * @param AddressFactory  $_addressFactory address factory
     * @param CustomerFactory $customerFactory customer factory
     */
    public function __construct(
        AddressFactory $_addressFactory,
        CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Authorization\Model\UserContextInterface $userContext
    ) {
        $this->addressFactory = $_addressFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->userContext = $userContext;
        parent::__construct($_addressFactory);
    }

    /**
     * Address validate
     *
     * @param AbstractAddress $address address
     *
     * @return array
     */
    public function validate(AbstractAddress $address): array
    {
        $errors = [];
        $addressId = $address instanceof QuoteAddressInterface ? $address->getCustomerAddressId() : $address->getId();
        if ($this->customerSession->getCustomer()->getId() == '') {
            $customer = $this->customerFactory->create()->load($this->userContext->getUserId());
            if ($addressId !== null && $customer->getCustomerType() != 3) {
                return parent::validate($address);
            } else {
                return $errors;
            }
        } else {
            if ($addressId !== null && $this->customerSession->getCustomer()->getCustomerType() != 3) {
                return parent::validate($address);
            } else {
                return $errors;
            }
        }

    }
}
