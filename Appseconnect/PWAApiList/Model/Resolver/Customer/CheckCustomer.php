<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\QuoteFactory;

class CheckCustomer implements ResolverInterface
{
    protected $quoteFactory;
    protected $storeManager;
    protected $helperData;
    public $_customerRepositoryInterface;

    // You can inject relevant classes in this constructor function.
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteFactory = $quoteFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $output = [];
        $customer = $value['model'];
        $customerId = (int)$customer->getId();

        $collection = $this->_customerRepositoryInterface->getById($customerId);
        // Get the custom attribute info of the customer.
        if ($customer->getCustomAttribute('customer_type')->getValue() == 3) {
            $parentid = $this->helperData->getCustomerId($customerId);
            $companyId = $parentid['customer_id'];
            $parentData = $this->_customerRepositoryInterface->getById($companyId);
            $output = $parentData->getCustomAttribute('customer_available_balance')->getValue();

            $quote = $this->quoteFactory->create()->loadByCustomer($customerId);
            $grandTotal = $quote->getGrandTotal();

            if ($output >= $grandTotal) {
                $output = true;
            } else {
                $output = false;
            }
        } else {
            $output = null;
        }
        return $output;
    }
}
