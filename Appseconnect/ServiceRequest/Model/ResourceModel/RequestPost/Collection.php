<?php
namespace Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'insync_service_request_collection';
    protected $_eventObject = 'entity_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\RequestPost', 'Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost');
    }

}
