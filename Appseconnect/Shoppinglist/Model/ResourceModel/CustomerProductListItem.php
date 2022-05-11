<?php
namespace Appseconnect\Shoppinglist\Model\ResourceModel;

use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;

class CustomerProductListItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_customer_product_list_map', 'entity_id');
    }

}
