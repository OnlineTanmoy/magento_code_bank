<?php

namespace Appseconnect\Shoppinglist\Block\Customer\Account\Mylist;

use Magento\Customer\Model\Session;

class EditList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var customerProductList
     */
    protected $customerProductList;

    /**
     * @var customerProductListItem
     */
    protected $customerProductListItem;

    /**
     * @var listId
     */
    protected $listId;

    /**
     * @var formKey
     */
    protected $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\Data\Form\FormKey $formKey,
        Session $customerSession,
        array $data = []
    )
    {
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->formKey = $formKey;

        parent::__construct($context, $data);

        if($this->getRequest()->getParam('id')) {
            $this->listId = $this->getRequest()->getParam('id');
        } else if(isset($data['listId'])) {
            $this->listId = $data['listId'];
        }
        if ($this->listId) {

            $listCollection = $this->customerProductListItem->create()->getCollection();
            $tableFrom = $listCollection->getSelect()->getPart('from');

            $collection = $productCollection->create()->addAttributeToSelect('*');

            if(isset($data['searchData'])) {
                $collection->addAttributeToFilter('name', array('like' => '%'. $data['searchData'] .'%'));
            }

            $collection->getSelect()
                ->joinLeft(
                    array('list' => $tableFrom['main_table']['tableName']),
                    "e.entity_id=list.product_id",
                    array('*'))
                ->where('list.list_id=' . $this->listId);

            $this->setCollection($collection);
        } else {
            $this->listId = 0;
        }

        //get collection of data
        $this->pageConfig->getTitle()->set(__(''));
    }

    public function getCustomerProductListItems()
    {
        if($this->getRequest()->getParam('id')) {
            $this->listId = $this->getRequest()->getParam('id');
        } else if(isset($data['listId'])) {
            $this->listId = $data['listId'];
        }
        if ($this->listId) {
            $listCollection = $this->customerProductListItem->create()->getCollection()
                ->addFieldToFilter('list_id', $this->listId);
            return $listCollection->getData();
        }
    }

    public function getList()
    {
        if (isset($this->listId)) {
            return $this->customerProductList->create()->load($this->listId);
        }

        return false;
    }

    public function getAttributeLabel($_product, $attributeName, $optionId)
    {
        $_attributeId = $_product->getResource()->getAttribute($attributeName);
        if ($_attributeId->usesSource()) {
            $_optionText = $_attributeId->getSource()->getOptionText($optionId);
        }
        return $_optionText;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
