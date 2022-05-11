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

namespace Appseconnect\AvailableToPromise\Model;

use Magento\Backend\App\Action;
use Appseconnect\AvailableToPromise\Api\ProductInStock\ProductInStockRepositoryInterface;
use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface as ProductInStockInterface;
use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockSearchResultsInterfaceFactory;
use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class ProductInStockRepository
 *
 * @category AvailableToPromise\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductInStockRepository implements ProductInStockRepositoryInterface
{
    /**
     * Available To Promise product resource
     *
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ProductInStockFactory
     */
    public $productInStockFactory;
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\CollectionFactory
     */
    public $productInStockCollectionFactory;

    public $productInStockSearchResults;
    /**
     * @var ProductInStockInterface
     */
    public $productInStockInterface;

    /**
     * ProductInStock interface
     *
     * @var \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterfaceFactory
     */
    public $productInStockInterfaceFactory;

    public $dataObjectHelper;

    public $dataObjectProcessor;

    public function __construct(
        \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory,
        \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\CollectionFactory $productInStockCollectionFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockSearchResultsInterfaceFactory $productInStockSearchResults,
        ProductInStockInterface $productInStockInterface,
        ProductInStockInterfaceFactory $productInStockInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->productInStockFactory = $productInStockFactory;
        $this->productInStockCollectionFactory = $productInStockCollectionFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->productInStockSearchResults = $productInStockSearchResults;
        $this->productInStockInterface = $productInStockInterface;
        $this->productInStockInterfaceFactory = $productInStockInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save Available To Promise Data
     *
     * @param ProductInStockInterface $productInStock
     * @return ProductInStockInterface
     */
    public function save(ProductInStockInterface $productInStock)
    {
        $productInStockArray = $this->extensibleDataObjectConverter
            ->toNestedArray($productInStock, [],
                'Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface');
        $productInStockModel = $this->productInStockFactory->create();
        $productInStockArray['posting_date'] = date('d-m-Y h:i:s');
        $productInStockModel->setData($productInStockArray)->save();

        return $this->get($productInStockModel->getId());
    }

    /**
     * Get Availavle To Promise Data
     *
     * @param int $availabletopromiseId
     * @return ProductInStockInterface
     */
    public function get($availabletopromiseId)
    {
        $availabletopromisedata = $this->productInStockCollectionFactory
            ->create()->addFieldToFilter('id', $availabletopromiseId)
            ->getFirstItem()
            ->getData();

        $this->productInStockInterface->setId($availabletopromisedata['id']);
        $this->productInStockInterface->setAvailableDate($availabletopromisedata['available_date']);
        $this->productInStockInterface->setProductSku($availabletopromisedata['product_sku']);
        $this->productInStockInterface->setQuantity($availabletopromisedata['quantity']);
        $this->productInStockInterface->setAvailableQuantity($availabletopromisedata['available_quantity']);
        $this->productInStockInterface->setDocumentType($availabletopromisedata['document_type']);
        $this->productInStockInterface->setPostingDate($availabletopromisedata['posting_date']);
        $this->productInStockInterface->setWarehouse($availabletopromisedata['warehouse']);
        $this->productInStockInterface->setSyncFlag($availabletopromisedata['sync_flag']);
        return $this->productInStockInterface;
    }

    /**
     * Search Available To Promise Data
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->productInStockSearchResults->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->productInStockFactory->create()->getCollection();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {

            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC)
                        ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $availabletopromise = array();
        foreach ($collection as $productInStockModel) {

            $productInStockData = $this->productInStockInterfaceFactory->create();
            $productInStockModelData = $productInStockModel->getData();
            $this->dataObjectHelper->populateWithArray(
                $productInStockData,
                $productInStockModelData,
                'Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface'
            );
            $availabletopromise[] = $this->dataObjectProcessor->buildOutputDataArray(
                $productInStockData,
                'Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface'
            );
        }

        $searchResults->setItems($availabletopromise);

        return $searchResults;
    }

    /**
     *
     * @param int $availabletopromiseId availabletopromise Id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($availabletopromiseId)
    {
        $availableToPromiseCollection = $this->productInStockFactory->create()->load($availabletopromiseId);

        if (!$availableToPromiseCollection->getId()) {
            throw new \Magento\Framework\Exception\InputException(__('AvailableToPromise with ID [' . $availabletopromiseId . '] does not exist.'));
        }
        $availableToPromiseCollection->delete();
        return true;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup filter group
     * @param \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\Collection $collection collection
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\Collection $collection
    ) {

        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

}
