<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Loadmoreitems extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $listId = $this->getRequest()->getParam('id');
        $layout = $this->_view->getLayout();
        $block = $layout->createBlock(\Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList::class);
        $block->setTemplate('Appseconnect_Shoppinglist::customer/account/mylist/loadmoreitems.phtml');
        $block->setData('id',$listId);
        $blockHtml = $block->toHtml();
        $resultJson->setData(array('html' =>$blockHtml));
        return $resultJson;
    }

}