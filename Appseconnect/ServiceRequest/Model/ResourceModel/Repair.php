<?php
namespace Appseconnect\ServiceRequest\Model\ResourceModel;

class Repair extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('insync_fixed_repaired', 'id');
    }

}
