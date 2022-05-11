<?php
namespace Appseconnect\ServiceRequest\Controller\Warranty;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->resultFactory = $resultFactory;
        return parent::__construct($context);
    }

    public function _prepareLayout()
    {
        //set page title
        $this->pageConfig->getTitle()->set(__('Manufacturer’s Warranty'));
        return parent::_prepareLayout();
    }

    public function execute()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied...'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        return $this->_pageFactory->create();
    }
}
