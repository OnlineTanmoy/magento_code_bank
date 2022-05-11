<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Adminhtml\Pricelist\Product;

/**
 * Class LoadBlock
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class LoadBlock extends \Magento\Backend\App\Action
{
    /**
     * Result raw
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory;
    
    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * LoadBlock constructor.
     *
     * @param \Magento\Backend\App\Action\Context             $context          context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory result raw
     * @param \Magento\Framework\View\LayoutFactory           $layoutFactory    layout
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
     * Load block load
     *
     * @return \Magento\Framework\View\LayoutFactory
     */
    public function execute()
    {
        $layout = $this->layoutFactory->create()
            ->createBlock(
                'Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab\View\ProductGrid',
                'price.product.grid'
            )
            ->toHtml();
        
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($layout);
    }
}
