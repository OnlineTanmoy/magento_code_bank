<?php

namespace Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap;

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
        $this->_init('Appseconnect\MultipleDiscounts\Model\DiscountMap',
            'Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap');
        $this->_map['fields']['id'] = 'main_table.id';
    }
}