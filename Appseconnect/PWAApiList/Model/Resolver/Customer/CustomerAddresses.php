<?php
declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\CustomerGraphQl\Model\Customer\Address\ExtractCustomerAddressData;

/**
 * Customers addresses field resolver
 */
class CustomerAddresses implements ResolverInterface
{
    /**
     * @var ExtractCustomerAddressData
     */
    private $extractCustomerAddressData;

    /**
     * @param ExtractCustomerAddressData $extractCustomerAddressData
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerFactory,
        ExtractCustomerAddressData $extractCustomerAddressData
    ) {
        $this->contactHelper = $contactHelper;
        $this->extractCustomerAddressData = $extractCustomerAddressData;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Customer $customer */
        $customer = $value['model'];

        $addressesData = [];

        if ($customer->getCustomAttribute('customer_type')->getValue() == 3) {
            $parentid = $this->contactHelper->getCustomerId($customer->getId());
            $companyId = $parentid['customer_id'];
            $parentData = $this->customerFactory->getById($companyId);
            $addresses = $parentData->getAddresses();
        } else {
            $addresses = $customer->getAddresses();
        }

        if (count($addresses)) {
            foreach ($addresses as $address) {
                $addressesData[] = $this->extractCustomerAddressData->execute($address);
            }
        }
        return $addressesData;
    }
}
