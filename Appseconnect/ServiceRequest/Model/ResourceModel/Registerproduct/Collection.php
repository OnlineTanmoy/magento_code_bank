<?php
namespace Appseconnect\ServiceRequest\Model\ResourceModel\Registerproduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'appseconnect_registerproduct_collection';
    protected $_eventObject = 'entity_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\Registerproduct', 'Appseconnect\ServiceRequest\Model\ResourceModel\Registerproduct');
    }

}
