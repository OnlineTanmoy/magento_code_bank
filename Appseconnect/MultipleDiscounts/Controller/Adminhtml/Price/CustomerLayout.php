<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

class CustomerLayout extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer Layout grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}