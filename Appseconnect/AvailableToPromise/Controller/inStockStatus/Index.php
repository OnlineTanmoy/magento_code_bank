<?php

namespace Appseconnect\AvailableToPromise\Controller\inStockStatus;

use Magento\Backend\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    public $helper;

    public function __construct(
        Context $context,
        \Appseconnect\AvailableToPromise\Helper\DeliveryDate\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
    }

    public function execute()
    {

        echo $this->helper->isDisable();

    }
}