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
namespace Appseconnect\B2BMage\Controller\Sales\Approve;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends Action\Action
{

    /**
     * Order loader
     *
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    public $orderLoader;

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * View constractor
     *
     * @param Action\Context       $context           context
     * @param OrderLoaderInterface $orderLoader       order loader
     * @param PageFactory          $resultPageFactory result page
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory
    ) {
        $this->orderLoader = $orderLoader;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Order view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        $resultPage = $this->resultPageFactory->create();
        
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        $status = $this->getRequest()->getParam('approver');
        if (isset($status) && $status == 'yes' && $navigationBlock) {
            $navigationBlock->setActive('b2bmage/sales/approve_listing');
        } elseif ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }
        
        return $resultPage;
    }
}
