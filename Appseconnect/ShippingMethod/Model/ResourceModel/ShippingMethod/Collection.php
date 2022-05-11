<?php

namespace Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\ShippingMethod\Model\ShippingMethod',
            'Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod');
    }
}