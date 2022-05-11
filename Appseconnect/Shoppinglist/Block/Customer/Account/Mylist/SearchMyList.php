<?php

namespace Appseconnect\Shoppinglist\Block\Customer\Account\Mylist;


use Magento\Customer\Model\Session;

class SearchMyList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var categoryCollection
     */
    protected $storeManager;

    /**
     * @var customerProductList
     */
    protected $customerProductList;

    /**
     *
     * @var Session
     */
    public $customerSession;

    /**
     * @var Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList
     */
    public $itemList;


    /**
     * @var \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory
     */
    public $customerProductListItem;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        //\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList $itemList,
        Session $customerSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->customerSession = $customerSession;
        $this->itemList = $itemList;

        $collection = $this->customerProductList->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());
        if (isset($this->_data['searchData'])) {

            $collection->addFieldToFilter('list_name', array('like' => '%' . $this->_data['searchData'] . '%'));
        }

        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'shoppinglist.grid.mylist.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }

    /**
     * @return string
     */
    // method for get pager html
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $listId
     */
    public function getPriceTotal($listId)
    {
        $listCollection = $this->customerProductListItem->create()->getCollection()
            ->addFieldToFilter('list_id', $listId);

        $totalPrice = 0;
        foreach ($listCollection as $item) {
            $totalPrice += $this->itemList->getItemPrice($item->getId()) * $item->getQty();
        }

        return $totalPrice;
    }
}
