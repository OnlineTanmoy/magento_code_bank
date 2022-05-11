<?php
namespace Appseconnect\Shoppinglist\Model;

class CustomerProductList extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\Shoppinglist\Model\ResourceModel\CustomerProductList');
    }
}
