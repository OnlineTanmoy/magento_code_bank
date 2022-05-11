<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\MultipleDiscounts\Model\ResourceModel\Discount\CollectionFactory;

/**
 * Class MassEnable
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    public $filter;
    
    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountFactory
     */
    public $multipleDiscountFactory;

    /**
     * @param Context $context
     * @param \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->filter = $filter;
        $this->multipleDiscountFactory = $multipleDiscountFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selectedIds = $this->getRequest()->getParam('selected');
        $excludedStatus = $this->getRequest()->getParam('excluded');
        if ($this->getRequest()->getParam('excluded') && $excludedStatus == 'false') {
            $multipleDiscountCollection = $this->collectionFactory->create();
            
            $deletedCount = count($multipleDiscountCollection);
            
            foreach ($multipleDiscountCollection as $multipleDiscount) {
                $this->bulkEnable($multipleDiscount->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $multipleDiscountId) {
                $this->bulkEnable($multipleDiscountId);
            }
        }
        
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been enabled.', $deletedCount)
        );
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * @param int $multipleDiscountId
     * @return void
     */
    private function bulkEnable($multipleDiscountId)
    {
        $model = $this->multipleDiscountFactory->create()->load($multipleDiscountId);
        $model->setIsActive(1);
        $model->save();
    }
}