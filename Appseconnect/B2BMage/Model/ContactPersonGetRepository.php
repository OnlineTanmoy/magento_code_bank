<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Appseconnect\B2BMage\Model\ResourceModel\Contact\CollectionFactory;
use Magento\Customer\Model\Customer\NotificationStorage;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class ContactPersonGetRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ContactPersonGetRepository
{
    /**
     * Contact collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Contact\CollectionFactory
     */
    public $contactCollection;
    
    /**
     * Customer factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Customer metadata
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    public $customerMetadata;
    
    /**
     * Search result factory
     *
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    
    /**
     * Extension attributes
     *
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    public $extensionAttributesJoinProcessor;

    /**
     * ContactPersonGetRepository constructor.
     *
     * @param CollectionFactory                                                $contactCollection                contact collection
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory                  customer
     * @param CustomerMetadataInterface                                        $customerMetadata                 customer metadata
     * @param \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory             search result
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor extenssion attribute
     */
    public function __construct(
        CollectionFactory $contactCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
    
        $this->searchResultsFactory = $searchResultsFactory;
        $this->contactCollection = $contactCollection;
        $this->customerFactory = $customerFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->customerMetadata = $customerMetadata;
    }

    /**
     * Get contact person data
     *
     * @param SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return mixed
     */
    public function getContactPersonData(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->customerFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, 'Magento\Customer\Api\Data\CustomerInterface');
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->customerMetadata->getAllAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        // Needed to enable filtering on name as a whole
        $collection->addNameToSelect();
        // Needed to enable filtering based on billing address attributes
        $collection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left');
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $customerId =  $this->contactCollection->create()->getContactPersonId($group, $collection);
        }
        if ($customerId != '') {
            $collection = $this->contactCollection->create()->contactFilter($collection, $customerId);
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {

            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection
                    ->addOrder(
                        $sortOrder->getField(),
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                    );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $customers = [];

        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }
}
