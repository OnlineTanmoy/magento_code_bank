<?php
namespace Appseconnect\ServiceRequest\Model\Source;

use Magento\Framework\DB\Ddl\Table;

class ServiceStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * @var \Appseconnect\ServiceRequest\Model\ResourceModel\StatusFactory
     */
    public $statusFactory;

    /**
     * SeriveStatus constructor.
     * @param \Appseconnect\ServiceRequest\Model\ResourceModel\StatusFactory $statusFactory
     */
    public function __construct(
        \Appseconnect\ServiceRequest\Model\StatusFactory $statusFactory
    )
    {
        $this->statusFactory = $statusFactory;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $data = array();

        $allStatus = $this->statusFactory->create()->getCollection();
        foreach($allStatus as $status){
            $label = ($status->getId() == 10) ? "Completed" : $status->getLabel();
            $data[] = array('value' => $status->getId(),
                'label' => $label);
        }
        $this->_options = $data;
        return $this->_options;
    }

    public function getOptionArray()
    {
        $data =array();

        foreach($this->getAllOptions() as $status){
            $data[$status['value']] =$status['label'];
        }
        return $data;
    }



    /**
     * Get a text for option value
     *
     * @param string|integer $value
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

    public function toOptionArray()
    {
        return $this->getAllOptions();
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
