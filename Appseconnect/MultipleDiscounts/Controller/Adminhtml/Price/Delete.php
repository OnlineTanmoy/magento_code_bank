<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Backend\App\Action;
use Appseconnect\MultipleDiscounts\Model\DiscountFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var DiscountFactory
     */
    public $multipleDiscountFactory;
    
    /**
     * @param Action\Context $context
     * @param DiscountFactory $multipleDiscountFactory
     */
    public function __construct(
        Action\Context $context,
        DiscountFactory $multipleDiscountFactory
    ) {
        parent::__construct($context);
        $this->multipleDiscountFactory = $multipleDiscountFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $multipleDiscountModel = $this->multipleDiscountFactory->create();
                $multipleDiscountModel->load($id);
                $multipleDiscountModel->delete();
                $this->messageManager->addSuccess(__('The Discount has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [
                    'id' => $id
                ]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Discount to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}