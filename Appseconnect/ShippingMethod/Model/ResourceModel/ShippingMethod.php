<?php

namespace Appseconnect\ShippingMethod\Model\ResourceModel;

class ShippingMethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('insync_shippingmethod', 'id');
    }
}