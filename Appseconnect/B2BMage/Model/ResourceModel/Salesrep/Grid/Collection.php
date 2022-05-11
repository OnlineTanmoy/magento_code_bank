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
namespace Appseconnect\B2BMage\Model\ResourceModel\Salesrep\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Appseconnect\B2BMage\Model\ResourceModel\Salesrep\Collection as SalesrepCollection;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends SalesrepCollection implements SearchResultInterface
{
    /**
     * AggregationInterface
     *
     * @var AggregationInterface
     */
    public $aggregations;
    
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    public $eventPrefix = '';
    
    /**
     * Name of event parameter
     *
     * @var string
     */
    public $eventObject = '';

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory EntityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger        Logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy FetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager  EventManager
     * @param $mainTable     MainTable
     * @param $eventPrefix   EventPrefix
     * @param $eventObject   EventObject
     * @param $resourceModel ResourceModel
     * @param Document                                                     $model         Model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection    Connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource      Resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        Document $model,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->eventPrefix = $eventPrefix;
        $this->eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }
    
    /**
     * GetAggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        $aggregations = $this->aggregations;
        return $aggregations;
    }

    /**
     * SetAggregations
     *
     * @param AggregationInterface $aggregationsValue AggregationsValue
     *
     * @return $this
     */
    public function setAggregations($aggregationsValue)
    {
        $this->aggregations = $aggregationsValue;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limitValue  LimitValue
     * @param int $offsetValue OffsetValue
     *
     * @return array
     */
    public function getAllIds($limitValue = null, $offsetValue = null)
    {
        return $this->getConnection()->fetchCol(
            $this->_getAllIdsSelect($limitValue, $offsetValue),
            $this->_bindParams
        );
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        $value = null;
        return $value;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return $this @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        $currentObject = $this;
        return $currentObject;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        $totalSize = $this->getSize();
        return $totalSize;
    }

    /**
     * Set total count.
     *
     * @param int $totalCount TotalCount
     *
     * @return $this @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        $currentObject = $this;
        return $currentObject;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items Items
     *
     * @return $this @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        $currentObject = $this;
        return $currentObject;
    }
}
