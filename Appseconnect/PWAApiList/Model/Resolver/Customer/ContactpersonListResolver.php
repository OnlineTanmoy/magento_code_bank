<?php


declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Customer;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Orders data reslover
 */
class ContactpersonListResolver implements ResolverInterface
{
    /**
     * @var CollectionFactoryInterface
     */
    private $collectionFactory;


    /**
     * @param CollectionFactoryInterface $collectionFactory
     * @param CheckCustomerAccount $checkCustomerAccount
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->contactHelper = $contactHelper;
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
        $items = [];

        $customer = $this->customerFactory->create()->load($context->getUserId());

        if ($this->contactHelper->isContactPerson($customer) && $this->contactHelper->isAdministrator($context->getUserId()) == 1) {
            $customerId = $this->contactHelper->getContactCustomerId($customer->getId());
            $contactPersonCollection = $this->contactHelper->contactPersonFactory->create()
                ->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('contactperson_id', array('nin' => array($customer->getId())));


            foreach ($contactPersonCollection as $contactPerson) {

                $contactData = $this->customerFactory->create()->load($contactPerson->getContactpersonId());

                $items[] = [
                    'name' => $contactData->getName(),
                    'email' => $contactData->getEmail(),
                    'contactperson_role' => $contactData->getContactpersonRole() == 1 ? 'Administrator' : 'Standard',
                    'status' => $contactPerson->getIsActive() == 1 ? 'Active' : 'Inactive',
                    'id' => $contactData->getId()
                ];
            }
        }

        return ['items' => $items];
    }
}
