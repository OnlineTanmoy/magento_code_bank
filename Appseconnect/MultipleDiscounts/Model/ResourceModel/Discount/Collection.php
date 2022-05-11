<?php

namespace Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount;

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
        $this->_init('Appseconnect\MultipleDiscounts\Model\Discount',
            'Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount');
        $this->_map['fields']['id'] = 'main_table.id';
    }
}