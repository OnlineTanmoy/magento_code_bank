<?php

namespace Appseconnect\ServiceRequest\Model;


use Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterfaceFactory;
use Appseconnect\ServiceRequest\Api\Repair\Data\RepairSearchResultsInterfaceFactory;
use Appseconnect\ServiceRequest\Api\Repair\RepairRepositoryInterface;
use Appseconnect\ServiceRequest\Model\RepairFactory;
use Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory as RepairCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RepairRepository
 * @package Appseconnect\RepairRequest\Model
 */
class RepairRepository extends \Magento\Framework\Model\AbstractModel implements RepairRepositoryInterface
{
    /**
     * @var \Appseconnect\RepairRequest\Model\RepairFactory
     */
    protected $repairFactory;
    /**
     * @var RepairCollectionFactory
     */
    protected $repairCollectionFactory;
    /**
     * @var RepairInterfaceFactory
     */
    protected $repairInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var repairSearchResultsInterfaceFactory
     */
    protected $repairSearchResultsInterfaceFactory;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * RequestPostRepository constructor.
     * @param \Appseconnect\ServiceRequest\Model\RepairFactory $repairFactory
     * @param RepairCollectionFactory $repairCollectionFactory
     * @param RepairInterfaceFactory $repairInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RepairSearchResultsInterfaceFactory $repairSearchResultsInterfaceFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        RepairFactory $repairFactory,
        RepairCollectionFactory $repairCollectionFactory,
        RepairInterfaceFactory $repairInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        RepairSearchResultsInterfaceFactory $repairSearchResultsInterfaceFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->repairFactory = $repairFactory;
        $this->repairCollectionFactory = $repairCollectionFactory;
        $this->repairInterfaceFactory = $repairInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->repairSearchResultsInterfaceFactory = $repairSearchResultsInterfaceFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Create Update Repair Request Data
     *
     * @param \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[] $repair
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData($repair)
    {
        $returnItems = array();
        foreach ($repair as $items) {
            $item = $this->extensibleDataObjectConverter
                ->toNestedArray($items, [], '\Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface');
            if (!isset($item['sku'])) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __("Please Provide SKU and try again")
                );
            } elseif (!isset($item['repair_cost'])) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __("Please Provide repair cost of SKU and try again")
                );
            }

            $requestModel = $this->repairFactory->create();
            if (isset($item['sku']) || isset($item['id'])) {
                // load existing repair item by SKU or ID
                $repairModule = null;
                if (isset($item['id'])) {
                    $repairModule = $this->repairFactory->create();
                    $repairModule->load($item['id']);
                } else if(isset($item['sku'])) {
                    $repairModule = $this->repairCollectionFactory->create()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('sku', $item['sku'])
                        ->getFirstItem();
                }

                if ($repairModule->getId()) {
                    $requestModel = $repairModule;
                    $requestModel->setData('sku', $item['sku']);
                    $requestModel->setData('repair_cost', $item['repair_cost']);
                    $requestModel->setData('product_description', $item['product_description']);
                } else {
                    if(isset($item['id'])) {
                        throw new \Magento\Framework\Exception\CouldNotSaveException(
                            __("Id [".$item['id']."] is not found in Warranty list")
                        );
                        return;
                    }
                    $requestModel->setData($item);
                }
            }

            try {
                $requestModel->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __("Error occurred, please try again.")
                );
            }
            $returnItems[] = $requestModel;
        }
        return $returnItems;
    }

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->repairSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->repairCollectionFactory->create();
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
            $testData = $this->repairInterfaceFactory->create();
            $testModelData = $testModel->getData();
            $this->dataObjectHelper->populateWithArray(
                $testData,
                $testModelData,
                'Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface'
            );
            $preorderItem[] = $this->dataObjectProcessor->buildOutputDataArray(
                $testData,
                'Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface'
            );
        }

        $searchResults->setItems($preorderItem);
        return $searchResults;
    }
}
