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
namespace Appseconnect\B2BMage\Model\Config\Source;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class Role
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Role extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $data = [
            [
                'value' => null,
                'label' => 'Select Option'
            ],
            [
                'value' => '2',
                'label' => 'Standard'
            ],
            [
                'value' => '1',
                'label' => 'Administrator'
            ]
        ];
        $this->_options = $data;
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value Value
     *
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
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
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Custom Attribute Options  ' . $attributeCode . ' column'
            ]
        ];
    }
}
