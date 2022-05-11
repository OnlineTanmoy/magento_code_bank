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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * Class LoadBlock
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class LoadBlock extends \Magento\Sales\Controller\Adminhtml\Order\Create
{

    /**
     * Result Raw
     *
     * @var RawFactory
     */
    public $resultRawFactory;

    /**
     * LoadBlock constructor.
     *
     * @param Action\Context                  $context              context
     * @param \Magento\Catalog\Helper\Product $productHelper        product helper
     * @param \Magento\Framework\Escaper      $escaper              escaper
     * @param PageFactory                     $resultPageFactory    result page
     * @param ForwardFactory                  $resultForwardFactory result forward
     * @param RawFactory                      $resultRawFactory     result raw
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        RawFactory $resultRawFactory
    ) {
    
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);
    }

    /**
     * Loading page block
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $request = $this->getRequest();
        
        $asJson = $request->getParam('json');
        $block = $request->getParam('block');
        
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('quotation_index_load_block_json');
        } else {
            $resultPage->addHandle('quotation_index_load_block_plain');
        }
        
        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && ! in_array('message', $blocks)) {
                $blocks[] = 'message';
            }
            
            foreach ($blocks as $block) {
                $resultPage->addHandle('quotation_index_load_block_' . $block);
            }
        }
        
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->resultRawFactory->create()->setContents($result);
    }
}
