<?php
namespace Appseconnect\ServiceRequest\Model;

class Status extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\ResourceModel\Status');
    }
}
