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
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context     $context           context
     * @param PageFactory $resultPageFactory result page
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_Salesrepresentative::salesrepresentative');
        $resultPage->addBreadcrumb(__('Manage Sales Representative'), __('Manage Sales Representative'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Sales Representative'));
        
        return $resultPage;
    }
}
