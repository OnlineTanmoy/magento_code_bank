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
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Comments
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Comments extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\Page
     */
    public $resultPageFactory;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Registry
     *
     * @var Registry
     */
    public $registry;
    
    /**
     * Quotation repository
     *
     * @var \Appseconnect\B2BMage\Model\QuotationRepository
     */
    public $quotationRepository;
    
    /**
     * Comment constructot
     *
     * @param Context                                         $context             context
     * @param Registry                                        $registry            registry
     * @param \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository quote repository
     * @param Session                                         $customerSession     customer session
     * @param PageFactory                                     $resultPageFactory   result page
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->quotationRepository = $quotationRepository;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Redirect to the Quick Order UI page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerSessionId = $this->customerSession->getCustomerId();
        if (! ($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        try {
            if (! $this->registry->registry('insync_current_customer_quote')) {
                $quote = $this->quotationRepository->get($quoteId);
                $this->registry->register('insync_current_customer_quote', $quote);
            }
        } catch (\Exception $e) {
            return $resultRedirect->setPath('b2bmage/quotation/index_history', []);
        }
        $resultPage = $this->resultPageFactory->create();
        
        $navigationBlock = $resultPage->getLayout()->getBlock('quotation_quote_edit');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2bmage/quotation/index_history');
        }
        return $resultPage;
    }
}
