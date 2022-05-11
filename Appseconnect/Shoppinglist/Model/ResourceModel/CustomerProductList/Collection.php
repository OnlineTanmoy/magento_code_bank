<?php
namespace Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductList;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     *
     * @var string
     */
    public $idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\Shoppinglist\Model\CustomerProductList',
            'Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductList'
        );
        $this->_map ['fields'] ['id'] = 'main_table.id';
    }
}
