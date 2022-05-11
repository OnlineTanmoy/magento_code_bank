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
 * Class Redirect
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Redirect extends \Magento\Backend\App\Action
{
    
    /**
     * Result page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Redirect constructor.
     *
     * @param Context     $context           context
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
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('b2bmage/contact/customerlist');
        return $resultRedirect;
    }
}
