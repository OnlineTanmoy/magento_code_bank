<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Mylist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Searchitemlist extends \Magento\Customer\Controller\AbstractAccount
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
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        ob_start();
        $searchData = $_REQUEST['search_data'];
        $listId = $_REQUEST['list_id'];
        $resultPage = $this->resultPageFactory->create();
        $loadProduct = $resultPage->getLayout()->createBlock('Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList',
            'customer.mylist.search',
            [
                'data' => [ 'searchData' => $searchData, 'listId' => $listId ]
            ]);
        $loadProduct->setTemplate('Appseconnect_Shoppinglist::customer/account/mylist/itemlist.phtml');

        echo $loadProduct->toHtml();
    }

}
