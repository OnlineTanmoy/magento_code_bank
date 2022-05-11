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
namespace Appseconnect\B2BMage\Model\ResourceModel\Customer;

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
    public $idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\B2BMage\Model\Customer',
            'Appseconnect\B2BMage\Model\ResourceModel\Customer'
        );
        $this->_map['fields']['id'] = 'main_table.id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
    
    /**
     * Add Value Sort To Collection Select
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection Collection
     * @param string                                                  $dir        Dir
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Source\Boolean
     */
    public function getSortToCollection($collection, $dir)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()
            ->getBackend()
            ->getTable();
        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()->joinLeft(
                [
                $tableName => $attributeTable
                ],
                "e.entity_id={$tableName}.entity_id" .
                " AND {$tableName}.attribute_id='{$attributeId}'" .
                " AND {$tableName}.store_id='0'",
                []
            );
            $valueExpr = $tableName . '.value';
        } else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    [
                    $valueTable1 => $attributeTable
                    ],
                    "e.entity_id={$valueTable1}.entity_id" .
                    " AND {$valueTable1}.attribute_id='{$attributeId}'" .
                    " AND {$valueTable1}.store_id='0'",
                    []
                )
                ->joinLeft(
                    [
                    $valueTable2 => $attributeTable
                    ],
                    "e.entity_id={$valueTable2}.entity_id" .
                    " AND {$valueTable2}.attribute_id='{$attributeId}'" .
                    " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    []
                );
            $valueExpr = $collection
                ->getConnection()
                ->getCheckSql($valueTable2 . '.value_id > 0', $valueTable2 . '.value', $valueTable1 . '.value');
        }
        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
