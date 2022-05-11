<?php
namespace Appseconnect\ServiceRequest\Controller\Form;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function _prepareLayout()
    {
        //set page title
        $this->pageConfig->getTitle()->set(__('Service Form'));
        return parent::_prepareLayout();
    }

    public function execute()
    {
        return $this->_pageFactory->create();
    }
}
