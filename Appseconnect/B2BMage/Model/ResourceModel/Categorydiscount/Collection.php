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

namespace Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * String
     *
     * @var string
     */
    public $idFieldName = 'categorydiscount_id';

    /**
     * ProductFactory
     *
     * @var ProductFactory
     */
    public $productFactory;

    /**
     * AttributeFactory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    public $attributeFactory;

    /**
     * Collection constructor.
     *
     * @param ProductFactory                                               $productFactory   ProductFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory    $attributeFactory AttributeFactory
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory    EntityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger           Logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy    FetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager     EventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection       Connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource         Resource
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->productFactory = $productFactory;
        $this->attributeFactory = $attributeFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\B2BMage\Model\Categorydiscount',
            'Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount'
        );
        $this->_map['fields']['categorydiscount_id'] = 'main_table.categorydiscount_id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Get AttributeFilter
     *
     * @param mixed $filterGroups FilterGroups
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getAttributeFilter($filterGroups)
    {
        $collection = $this->productFactory->create()->getCollection();
        $collection->joinTable(
            $this->_resources->getTableName('cataloginventory_stock_item'),
            'product_id=entity_id',
            ['*'],
            null,
            'left'
        );
        foreach ($filterGroups as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
            }
        }
        foreach ($fields as $fieldValue) {
            $attr = $this->attributeFactory->create()
                ->loadByCode($entity, $fieldValue['attribute']);

            if ($attr->getId()) {
                $collection->addAttributeToFilter($fieldValue['attribute'], $fieldValue['eq']);
            } else {
                $collection->getSelect()->where("" . $fieldValue['attribute'] . "=" . $fieldValue['eq']);
            }
        }
        return $collection;
    }
}
