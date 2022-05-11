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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Salesrep;

use Magento\Backend\App\Action\Context;

/**
 * Class Customergrid
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customergrid extends \Magento\Backend\App\Action
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
     * Customergrid constructor.
     *
     * @param Context                                         $context          context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory result raw
     * @param \Magento\Framework\View\LayoutFactory           $layoutFactory    layout
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
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Customer\Grid',
                'salesrepresentative_customer_edit_tabab'
            )->toHtml()
        );
    }
}
