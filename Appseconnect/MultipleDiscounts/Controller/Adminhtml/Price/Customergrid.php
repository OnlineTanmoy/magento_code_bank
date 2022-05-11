<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Backend\App\Action\Context;

class Customergrid extends \Magento\Backend\App\Action
{
     /**
      * @var \Magento\Framework\Controller\Result\RawFactory
      */
    public $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Grid Action
     * Display list of customers related to current discount
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount\Edit\Tab\View\Grid',
                'discount_customer_edit_tab'
            )->toHtml()
        );
    }
}