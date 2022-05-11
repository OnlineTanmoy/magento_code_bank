<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing\Order;

class LoadBlock extends \Magento\Backend\App\Action
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
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\LayoutFactory
     */
    public function execute()
    {
        $layout = $this->layoutFactory->create()
            ->createBlock(
                'Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab\View\OrderGrid',
                'price.product.grid'
            )
            ->toHtml();
        
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($layout);
    }
}
