<?php

namespace Appseconnect\MultipleDiscounts\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory;
use Magento\Framework\DataObject;

class Status extends AbstractRenderer
{
    /**
     * @var CollectionFactory
     */
    public $multipleDiscountCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param CollectionFactory $multipleDiscountCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        CollectionFactory $multipleDiscountCollectionFactory,
        array $data = []
    ) {
        $this->multipleDiscountCollectionFactory = $multipleDiscountCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $customer = $this->multipleDiscountCollectionFactory->create();
        $customer->addFieldToFilter('customer_id', $row->getId());
        $customer->addFieldToFilter('parent_id', $this->getRequest()->getParam('id'));
        $output = $customer->getData();
        $result = [];
        if (! empty($output)) {
            return 'Assigned';
        } else {
            return "Unassigned";
        }
    }
}