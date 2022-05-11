<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory;

class Unassigncustomers extends \Magento\Backend\App\Action
{
    /**
     *
     * @var Filter
     */
    public $filter;

    /**
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory
     */
    public $discountMapFactory;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory $discountMapFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->customerFactory = $customerFactory;
        $this->discountMapFactory = $discountMapFactory;
        $this->filter = $filter;
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
        $requestData = $this->getRequest()->getPostValue();
        $parentId = $this->getRequest()->getParam('parent_id');

        foreach ($requestData['customer_id'] as $key => $value) {
            $customerName = $this->loadCustomer($value)->getName();
            $customer = $this->collectionFactory->create()
                ->addFieldToFilter('parent_id', $parentId)
                ->addFieldToFilter('customer_id', $value);
            if ($customer->getData()) {
                foreach ($customer->getData() as $data) {
                    $this->unmap($data);
                    $this->messageManager->addSuccess(__(
                        '%1 has been unassigned',
                        $customerName
                    ));
                }
            } else {
                $this->messageManager->addError(__(
                    '%1 is already unassigned',
                    $customerName
                ));
            }
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('multiplediscount/price/edit', [
            'id' => $this->getRequest()
                ->getParam('id')
        ]);
    }

    /**
     * @param int $id
     * @return \Magento\Customer\Model\CustomerFactory
     */
    private function loadCustomer($id)
    {
        $customer = $this->customerFactory->create()->load($id);
        return $customer;
    }

    /**
     * @param array $data
     * @return void
     */
    private function unmap($data)
    {
        $model = $this->discountMapFactory->create();
        $model->load($data['id']);
        $model->delete();
    }
}