<?php
namespace Appseconnect\MultipleDiscounts\Model;

use Magento\Framework\DataObject\IdentityInterface;

class DiscountMap extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap');
    }
}