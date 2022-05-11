<?php
namespace Appseconnect\ShippingMethod\Model;

use Magento\Framework\DataObject\IdentityInterface;

class ShippingMethod extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod');
    }
}