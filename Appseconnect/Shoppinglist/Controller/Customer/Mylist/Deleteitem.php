<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Deleteitem extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var customerProductList
     */
    protected $customerProductList;

    /**
     * @var customerProductListItem
     */
    protected $customerProductListItem;

    /**
     * @var messageManager
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->messageManager = $messageManager;
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
        $listItem = $this->customerProductListItem->create()->load($postData['id']);


        $list = $this->customerProductList->create()->load($listItem->getListId());
        $list->setItem($list->getItem()-1)
            ->setTotalPrice($list->getTotalPrice()-$listItem->getTotalPrice())->save();

        $listItem->delete();

        $this->messageManager->addSuccess(__('Successfully deleted the item from list.'));
        $this->_redirect('shoppinglist/customer/account_editlist/', array('id' => $list->getId()));
    }

}