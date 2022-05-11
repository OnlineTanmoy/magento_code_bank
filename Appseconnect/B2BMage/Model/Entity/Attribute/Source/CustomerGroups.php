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

namespace Appseconnect\B2BMage\Model\Entity\Attribute\Source;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class CustomerGroups
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerGroups extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Option values
     */
    const VALUE_YES = 1;

    const VALUE_NO = 0;

    /**
     * AttributeFactory
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory
     */
    public $eavAttrEntity;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryVisibility\Data
     */
    public $categoryVisibilityHelper;

    /**
     * CollectionFactory
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory
     */
    public $customerCollection;

    /**
     * CustomerGroups constructor.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory             $eavAttrEntity            EavAttrEntity
     * @param \Appseconnect\B2BMage\Helper\CategoryVisibility\Data                 $categoryVisibilityHelper CategoryVisibilityHelper
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory $customerCollection       CustomerCollection
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity,
        \Appseconnect\B2BMage\Helper\CategoryVisibility\Data $categoryVisibilityHelper,
        \Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory $customerCollection
    ) {
        $this->eavAttrEntity = $eavAttrEntity;
        $this->categoryVisibilityHelper = $categoryVisibilityHelper;
        $this->customerCollection = $customerCollection;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $groupOptions = $this->categoryVisibilityHelper->getCustomerGroups();
        return $groupOptions;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_optionsData = [];
        foreach ($this->getAllOptions() as $_optionData) {
            $_optionsData[] = $_optionData['label'];
        }
        return $_optionsData;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $requestValue RequestValue
     *
     * @return string|false
     */
    public function getOptionText($requestValue)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $requestValue) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => true,
                'comment' => $attributeCode . ' column'
            ]
        ];
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $indexes = [];
        $index = 'IDX_' . strtoupper($attributeCode);
        $indexes[$index] = [
            'type' => 'index',
            'fields' => [
                $attributeCode
            ]
        ];
        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store Store
     *
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        $attribute = $this->getAttribute();
        return $this->eavAttrEntity->create()->getFlatUpdateSelect($attribute, $store);
    }

    /**
     * Get a text for index option value
     *
     * @param string|int $requestValue RequestValue
     *
     * @return string|bool
     */
    public function getIndexOptionText($requestValue)
    {
        switch ($requestValue) {
        case self::VALUE_YES:
            return 'Yes';
        case self::VALUE_NO:
            return 'No';
        }

        return parent::getIndexOptionText($requestValue);
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collectionData CollectionData
     * @param string                                                  $dir            Dir
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Source\Boolean
     */
    public function addValueSortToCollection($collectionData, $dir = \Magento\Framework\DB\Select::SQL_ASC)
    {
        return $this->customerCollection->create()->getSortToCollection($collectionData, $dir);
    }
}
