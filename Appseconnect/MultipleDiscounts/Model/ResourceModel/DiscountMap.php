<?php

namespace Appseconnect\MultipleDiscounts\Model\ResourceModel;

class DiscountMap extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('insync_multiple_discount_map', 'id');
    }

    public function removeMapping($id)
    {
        $connection = $this->getConnection();

        try {
            $connection->delete($this->_resources->getTableName('insync_multiple_discount_map'), [
                'parent_id = ?' => $id
            ]);
        } catch (\Exception $e) {
            throw new NotFoundException(__('Something went wrong while mapping the customers.'));
        }

        return true;
    }
}