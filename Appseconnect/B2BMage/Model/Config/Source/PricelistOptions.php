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

use Magento\Framework\App\Request\Http;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class PricelistOptions
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class PricelistOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Http
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * Data
     *
     * @var \Appseconnect\ContectPerson\Helper\Data
     */
    public $contactPersonHelper;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helper;

    /**
     * PricelistOptions constructor.
     *
     * @param Http                                            $request             Request
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper ContactPersonHelper
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data     $helper              Helper
     */
    public function __construct(
        Http $request,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helper
    ) {
        $this->contactPersonHelper = $contactPersonHelper;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $data = $this->helper->getPricelist();
        
        $customerId = $this->request->getParam('id');
        if ($customerId) {
            $isB2B = $this->contactPersonHelper->isB2Bcustomer($customerId);
            if (! $isB2B) {
                $data = [];
            }
        }
        
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
