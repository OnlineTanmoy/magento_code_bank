<?php
namespace Appseconnect\ServiceRequest\Model;

class Serial extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\ResourceModel\Serial');
    }
}
