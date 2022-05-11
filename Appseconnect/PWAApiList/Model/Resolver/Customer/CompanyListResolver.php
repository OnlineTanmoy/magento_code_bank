<?php
declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Company List reslover
 */
class CompanyListResolver implements ResolverInterface
{
    /**
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesrepFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $salesrepHelper;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactHelper;

    /**
     * @param \Appseconnect\B2BMage\Model\SalesrepFactory $salesrepFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrepHelper
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\SalesrepFactory $salesrepFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrepHelper,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper
    ) {
        $this->salesrepFactory = $salesrepFactory;
        $this->customerFactory = $customerFactory;
        $this->salesrepHelper = $salesrepHelper;
        $this->contactHelper = $contactHelper;
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

        $items = [];
        $salesRepId = $context->getUserId();
        $checkCustomer = $this->salesrepHelper->isSalesrep($salesRepId);
        $salesrepStatus = $this->customerFactory->create()->load($salesRepId)->getCustomerStatus();

        $salesrepGridId = $this->salesrepHelper->salesrepGridFactory->create()
            ->getCollection()
            ->addFieldToFilter('salesrep_customer_id', $salesRepId)
            ->addFieldToSelect('id')
            ->getData();

        $companyCollection = [];
        if ($checkCustomer && $salesrepStatus) {
            $companyCollection = $this->salesrepHelper->salesrepFactory->create()
                ->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('salesrep_id', $salesrepGridId);
        }

        foreach ($companyCollection as $company) {
            $companyData = $this->customerFactory->create()->load($company->getCustomerId());
            $contactPersonCollection = $this->contactHelper->contactPersonFactory->create()
                ->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $company->getCustomerId());

            $newitems = [];
            foreach ($contactPersonCollection as $contactPerson) {
                $contactData = $this->customerFactory->create()->load($contactPerson->getContactpersonId());
                $newitems[] = [
                    'name' => $contactData->getName(),
                    'email' => $contactData->getEmail(),
                    'contactperson_role' => $contactData->getContactpersonRole() == 1 ? 'Administrator' : 'Standard',
                    'status' => $contactPerson->getIsActive() == 1 ? 'Active' : 'Inactive',
                    'id' => $contactData->getId()
                ];
            }

            $items[] = [
                'name' => $companyData->getName(),
                'email' => $companyData->getEmail(),
                'status' => $companyData->getCustomerStatus() == 1 ? 'Active' : 'Inactive',
                'id' => $companyData->getId(),
                'companycontactpersondata' => $newitems
            ];
        }

        return ['companydata' => $items];
    }
}