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
use Magento\Framework\App\Request\Http;

/**
 * Class CustomerType
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Http
     *
     * @var Http
     */
    public $request;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helper;

    /**
     * CustomerType constructor.
     *
     * @param Http                                            $request Request
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helper  Helper
     */
    public function __construct(Http $request, \Appseconnect\B2BMage\Helper\ContactPerson\Data $helper)
    {
        $this->request = $request;
        $this->contactPersonHelper = $helper;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $customerId = $this->request->getParam('id');
        $data = [];
        $isB2B = $this->contactPersonHelper->isB2Bcustomer($customerId);
        if (! $isB2B || ! $customerId) {
            $data[] = [
                'value' => '1',
                'label' => 'B2C Customer'
            ];
        }
        $data[] = [
            'value' => '4',
            'label' => 'B2B Customer'
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
