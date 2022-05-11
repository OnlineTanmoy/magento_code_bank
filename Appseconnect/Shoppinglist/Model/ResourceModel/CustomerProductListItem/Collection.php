<?php
namespace Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductListItem;

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
            'Appseconnect\Shoppinglist\Model\CustomerProductListItem',
            'Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductListItem'
        );
        $this->_map ['fields'] ['id'] = 'main_table.id';
    }
}
