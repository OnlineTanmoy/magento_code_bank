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

namespace Appseconnect\CompanyDivision\Model\ResourceModel;

/**
 * Class Contact
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Division extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Primarey key for catalog_category_entity_text table
     *
     * @var string
     */
    public $primarekey = "id";

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_division', 'id');
    }

    /**
     * GetContactCollection
     *
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Contact\Collection $contactData ContactData
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Contact\Collection
     */
    public function getContactCollection($contactData)
    {
        $contactData->getSelect()->join(
            ['customer' => $this->_resources->getTableName('customer_entity')],
            'customer.entity_id = main_table.contactperson_id',
            [
                'firstname',
                'entity_id',
                'email',
                'lastname'
            ]
        );

        return $contactData;
    }

    /**
     * GetRowData
     *
     * @param int $contactId ContactId
     *
     * @return mixed
     */
    public function getRowData($contactId)
    {
        $result = null;
        $connection = $this->getConnection();
        $bind = [
            'id' => $contactId
        ];

        $select = $connection->select()
            ->from($this->_resources->getTableName('insync_division'))
            ->where('id = :id');

        $result = $connection->fetchRow($select, $bind);

        return $result;
    }

    /**
     * FetchCustomerTypeAttribute
     *
     * @param string $attributeCode AttributeCode
     *
     * @return mixed
     */
    public function fetchCustomerTypeAttribute($attributeCode)
    {
        $connection = $this->getConnection();
        $bind = [
            'attribute_code' => $attributeCode
        ];

        $select = $connection->select()
            ->from($this->_resources->getTableName('eav_attribute'), ['attribute_id'])
            ->where('attribute_code = :attribute_code');

        $result = $connection->fetchAll($select, $bind);
        return $result;
    }

    /**
     * UpdateCustomerTypeAttribute
     *
     * @param string $attributeCode AttributeCode
     *
     * @return void
     */
    public function updateCustomerTypeAttribute($attributeCode)
    {
        $connection = $this->getConnection();
        $eavAttribute = $this->_resources->getTableName('eav_attribute');
        if ($attributeCode == 'customer_type') {
            $bind = [
                'source_model' => 'Appseconnect\B2BMage\Model\Config\Source\CustomerType',
                'frontend_input' => 'select'
            ];
        } elseif ($attributeCode == 'customer_status') {
            $bind = [
                'frontend_input' => 'select',
                'source_model' => 'Appseconnect\B2BMage\Model\Config\Source\Options',
                'frontend_label' => 'Status'
            ];
        }

        $where = [
            'attribute_code = ?' => $attributeCode
        ];
        $connection->update($eavAttribute, $bind, $where);
    }

    /**
     * UpdateCustomerEavAttribute
     *
     * @param int     $attributeId AttributeId
     * @param boolean $isBind      IsBind
     *
     * @return void
     */
    public function updateCustomerEavAttribute($attributeId, $isBind = false)
    {
        $connection = $this->getConnection();
        $customerEavAttribute = $this->_resources->getTableName('customer_eav_attribute');

        if ($isBind) {
            $bind = [
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
            ];
        } else {
            $bind = [
                'is_visible' => 1
            ];
        }
        $where = [
            'attribute_id = ?' => $attributeId
        ];
        $connection->update($customerEavAttribute, $bind, $where);
    }

    /**
     * GetProductList
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection  Collection
     * @param int                                                     $groupId     GroupId
     * @param int                                                     $attributeId AttributeId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductList($collection, $groupId, $attributeId)
    {
        $catalogCategoryTable = $this->_resources->getTableName('catalog_category_entity_text');
        if ($this->getConnection()->tableColumnExists($catalogCategoryTable, "row_id") && $this->primarekey == "entity_id") {
            $this->primarekey = "row_id";
        }
        $collection->getSelect()->where(
            'e.entity_id IN (?)',
            new \Zend_Db_Expr(
                $this->getConnection()->select()->from(
                    ['pr' => $this->_resources->getTableName('catalog_category_product')],
                    ['product_id']
                )->where(
                    'pr.category_id IN (?)',
                    new \Zend_Db_Expr(
                        $this->getConnection()->select()->from(
                            ['ct' =>
                                $catalogCategoryTable],
                            [$this->primarekey]
                        )->where(
                            'ct.attribute_id =' . $attributeId . ' AND 
                                    FIND_IN_SET(' . $groupId . ', ct.value)'
                        )
                    )
                )
            )
        );

        return $collection;
    }

    /**
     * GetCateogoryList
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $collection  Collection
     * @param int                                                      $groupId     GroupId
     * @param int                                                      $attributeId AttributeId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCateogoryList($collection, $groupId, $attributeId)
    {
        $getMainTableAlias = $collection->getSelect()
            ->getPart(\Magento\Framework\DB\Select::FROM);
        $getMainTableAlias = array_key_exists('main_table', $getMainTableAlias) ?
            'main_table' : 'e';

        $catalogCategoryTable = $this->_resources->getTableName('catalog_category_entity_text');
        if ($this->getConnection()->tableColumnExists($catalogCategoryTable, "row_id") && $this->primarekey == "entity_id") {
            $this->primarekey = "row_id";
        }

        $collection->getSelect()
            ->where(
                $getMainTableAlias . '.' . $this->primarekey . ' IN (?)',
                new \Zend_Db_Expr(
                    $this->getConnection()->select()->from(
                        ['catText' =>
                            $catalogCategoryTable],
                        [$this->primarekey]
                    )
                        ->where(
                            'catText.attribute_id="' . $attributeId . '" AND  
                        FIND_IN_SET("' . $groupId . '", catText.value)'
                        )
                        ->group('catText.' . $this->primarekey . '')
                )
            );

        return $collection;
    }
}
