<?php

namespace Appseconnect\Shoppinglist\Block\Customer\Account\Dashboard;

use Magento\Customer\Model\Session;

class CustomerProductList extends \Magento\Framework\View\Element\Template
{
    protected $customerProductList;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        Session $customerSession,
        array $data = []
    )
    {
        $this->customerProductList = $customerProductList;
        parent::__construct($context, $data);
        //get collection of data
        $collection = $this->customerProductList->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerSession->getCustomerId());
        $this->setCollection($collection);
       $this->pageConfig->getTitle()->set(__(''));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'shoppinglist.grid.productlist.pager'
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
}

?>