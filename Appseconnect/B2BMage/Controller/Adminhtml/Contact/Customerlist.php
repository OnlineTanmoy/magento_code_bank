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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Contact;

use Appseconnect\B2BMage\Controller\Adminhtml\Contact;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Customerlist
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customerlist extends \Magento\Backend\App\Action
{
    /**
     * Result page factory variable
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Customerlist constructor.
     *
     * @param Context     $context           contaxt
     * @param PageFactory $resultPageFactory result page factory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Action function
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_ContactPerson::customers');
        $resultPage->addBreadcrumb(__('Customers'), __('Customers'));
        $resultPage->addBreadcrumb(__('Manage Customers'), __('Manage Customers'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Customers'));
        return $resultPage;
    }
}
