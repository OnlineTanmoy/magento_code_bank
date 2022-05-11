<?php
namespace Appseconnect\ServiceRequest\Model\ResourceModel;

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_service_status', 'id');
    }


    /**
     * @param int $contactId
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
            ->from($this->_resources->getTableName('insync_service_status'))
            ->where('id = :id');
        
        $result = $connection->fetchRow($select, $bind);
        
        return $result;
    }



}
