<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\SalesRep;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Salesrepgrid\CollectionFactory as SalesrepGridCollectionFactory;

/**
 * Create customer token from customer email
 */
class GenerateTokenForSalesrep implements ResolverInterface
{
    /**
     * @var SalesrepHelper
     */
    public $salesrepHelper;
    /**
     * @var ContactHelper
     */
    public $contactHelper;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var TokenFactory
     */
    private $tokenModelFactory;

    /**
     * @param TokenFactory $tokenModelFactory
     * @param CustomerFactory $customerFactory
     * @param SalesrepHelper $salesrepHelper
     * @param ContactHelper $contactHelper
     * @param SalesrepGridCollectionFactory $salesrepGridCollectionFactory
     * @param CollectionFactory $salesrepCollectionFactory
     */
    public function __construct(
        TokenFactory $tokenModelFactory,
        CustomerFactory $customerFactory,
        CollectionFactory $salesrepCollectionFactory,
        SalesrepGridCollectionFactory $salesrepGridCollectionFactory,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrepHelper,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->customerFactory = $customerFactory;
        $this->salesrepCollectionFactory = $salesrepCollectionFactory;
        $this->SalesrepGridCollectionFactory = $salesrepGridCollectionFactory;
        $this->salesrepHelper = $salesrepHelper;
        $this->contactHelper = $contactHelper;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $currentId = $context->getUserId();
        $email = $args['input']['email'];
        $customer = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect("entity_id")
            ->addAttributeToFilter("email", $email)
            ->getFirstItem();
        if ($currentId && !empty($customer->getData())) {
            if ($this->salesrepHelper->isSalesrep($currentId) && $this->contactHelper->isContactPerson($customer)) {
                $salesrepGridCollection = $this->SalesrepGridCollectionFactory->create()
                    ->addFieldtoFilter('salesrep_customer_id', $currentId)
                    ->getFirstItem();
                $b2bCustomerId = $this->contactHelper->getCustomerId($customer->getId())['customer_id'];
                $b2bCustomerStatus = $this->customerFactory->create()
                    ->load($b2bCustomerId)
                    ->getCustomerStatus();
                if ($salesrepGridCollection['is_active'] && ($customer['is_active'] && $b2bCustomerStatus)) {
                    $salesrepId = $salesrepGridCollection->getId();
                    $salesrepCollection = $this->salesrepCollectionFactory->create()
                        ->addFieldtoFilter('customer_id', $b2bCustomerId)
                        ->addFieldtoFilter('salesrep_id', $salesrepId)
                        ->getFirstItem();
                    $isAssignCustomer = $salesrepCollection->getData();
                    if (!empty($isAssignCustomer)) {
                        $contactpersonemail = $email;
                        $contactpersontoken = $this->tokenModelFactory->create()
                            ->createCustomerToken($customer->getId())->getToken();
                        return [
                            "contactperson_email" => $contactpersonemail,
                            "contactperson_token" => $contactpersontoken
                        ];
                    }
                } else {
                    throw new GraphQlInputException(__("Customer is Inactive"));
                }
            }
        }
    }
}
