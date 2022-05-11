<?php
namespace Appseconnect\ServiceRequest\Controller\Repair;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Listing
 * @package Appseconnect\ServiceRequest\Controller\Repair
 */
class Listing extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var ResultFactory
     */
    public $resultFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        ResultFactory $resultFactory
    ) {

        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    /**
     * Repair List
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
