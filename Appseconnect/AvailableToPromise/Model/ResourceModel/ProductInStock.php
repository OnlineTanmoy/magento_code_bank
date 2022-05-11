<?php

namespace Appseconnect\AvailableToPromise\Model\ResourceModel;

class ProductInStock extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('insync_availabletopromise', 'id');
    }
}