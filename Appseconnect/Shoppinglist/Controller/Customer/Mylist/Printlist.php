<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Printlist extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var messageManager
     */
    protected $messageManager;

    /**
     * @var myListHelper
     */
    protected $myListHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Appseconnect\Shoppinglist\Helper\Mylist\Data $myListHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->myListHelper = $myListHelper;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postData = $this->getRequest()->getParams();
        $this->myListHelper->getListPrint($postData['list_id']);

        return false;
    }

}