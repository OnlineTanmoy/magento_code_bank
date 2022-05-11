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
namespace Appseconnect\B2BMage\Controller\Quotation\AbstractController;

use Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory;
use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Approve
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class Approve extends Action\Action
{

    /**
     * Quote
     *
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Qutation service
     *
     * @var \Appseconnect\B2BMage\Model\Service\QuotationService
     */
    public $quotationService;
    
    /**
     * Customer constractor
     *
     * @param Action\Context                                       $context           context
     * @param \Appseconnect\B2BMage\Model\Service\QuotationService $quotationService  quotation service
     * @param \Appseconnect\B2BMage\Model\QuoteFactory             $quoteFactory      quote
     * @param PageFactory                                          $resultPageFactory result page
     */
    public function __construct(
        Action\Context $context,
        \Appseconnect\B2BMage\Model\Service\QuotationService $quotationService,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        PageFactory $resultPageFactory
    ) {
        $this->quotationService = $quotationService;
        $this->quoteFactory = $quoteFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Submit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        try {
            $this->quotationService->approveQuoteById($quoteId);
            $this->messageManager->addSuccess(__('You have successfully approved the quote.'));
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while approving the quote.')
            );
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(
            'b2bmage/quotation/index_view', [
            'quote_id' => $quoteId,
            '_current' => true
            ]
        );
        return $resultRedirect;
    }
}
