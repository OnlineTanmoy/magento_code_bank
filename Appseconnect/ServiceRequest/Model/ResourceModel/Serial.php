<?php
namespace Appseconnect\ServiceRequest\Model\ResourceModel;

class Serial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('insync_product_serial', 'id');
    }

}
