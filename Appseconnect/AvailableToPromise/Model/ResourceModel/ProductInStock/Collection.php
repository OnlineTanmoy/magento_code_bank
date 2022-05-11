<?php

namespace Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock;

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
        $this->_init('Appseconnect\AvailableToPromise\Model\ProductInStock',
            'Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock');
    }
}