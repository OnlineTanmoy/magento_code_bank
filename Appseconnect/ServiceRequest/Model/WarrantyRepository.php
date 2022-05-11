<?php
namespace Appseconnect\ServiceRequest\Model;


use Magento\Framework\Exception\CouldNotSaveException;
use Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantySearchResultsInterfaceFactory;
use Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory as WarrantyPostCollectionFactory;

class WarrantyRepository implements \Appseconnect\ServiceRequest\Api\Warranty\WarrantyRepositoryInterface
{

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    public function __construct(
        \Appseconnect\ServiceRequest\Model\WarrantyFactory $warrantyFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        WarrantyInterfaceFactory $warrantyInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        WarrantySearchResultsInterfaceFactory $warrantySearchResultsInterfaceFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        WarrantyPostCollectionFactory $warrantyPostCollectionFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
    ) {
        $this->warrantyFactory = $warrantyFactory;
        $this->customerFactory = $customerFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->warrantyInterfaceFactory = $warrantyInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->warrantySearchResultsInterfaceFactory = $warrantySearchResultsInterfaceFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->warrantyPostCollectionFactory = $warrantyPostCollectionFactory;
        $this->contactPersonHelper = $contactPersonHelper;
    }
    

    /**
     * {@inheritDoc}
     * @see \Appseconnect\ServiceRequest\Api\warranty\WarrantyRepositoryInterface::save()
     */
    public function save(\Appseconnect\ServiceRequest\Api\warranty\Data\WarrantyInterface $warranty)
    {
        $customerId = $warranty->getCustomerId();
        $customerName = $this->customerFactory->create()
            ->load($customerId)
            ->getName();
        $warrantyDataArray = $this->extensibleDataObjectConverter
        ->toNestedArray($warranty, [], 'Appseconnect\ServiceRequest\Api\warranty\Data\WarrantyInterface');
        //$validate = $this->dataValidator($warrantyDataArray);
        if (!($this->customerFactory->create()
            ->load($customerId)
            ->getEntityId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        } else if(!$this->contactPersonHelper->isB2Bcustomer($customerId)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("This customer id is not b2b customer", $customerId)
            );
        }
        $warrantyModel = $this->warrantyFactory->create();

        $warrantyDataArray['is_active'] = (in_array($warrantyDataArray['status'], ["active", "onhold", "draft"])) ? 1 : 0 ;

        if($warrantyDataArray['contract_status'] == 'A') {
            $now = time();
            $endDate = strtotime($warrantyDataArray['warranty_end_date']);
            $dateDiff = $endDate - $now;
        }

        if($customerId) {
            $customer = $this->customerFactory->create()
                ->load($customerId);
            $warrantyDataArray['customer_name'] = $customer->getFirstname().' '.$customer->getLastname();
        }

        $warrantyModel->setData($warrantyDataArray);
        $warrantyModel->save();
        $warrantyId = $warrantyModel->getId();
        $warranty->setId($warrantyId);

        return $warranty;
    }

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->warrantySearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->warrantyPostCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrdersData = $searchCriteria->getSortOrders();
        if ($sortOrdersData) {
            foreach ($sortOrdersData as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $preorderItem = array();
        foreach ($collection as $testModel) {
            $testData = $this->warrantyInterfaceFactory->create();
            $testModelData = $testModel->getData();
            $this->dataObjectHelper->populateWithArray(
                $testData,
                $testModelData,
                'Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface'
            );
            $preorderItem[] = $this->dataObjectProcessor->buildOutputDataArray(
                $testData,
                'Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface'
            );
        }

        $searchResults->setItems($preorderItem);
        return $searchResults;
    }

}
