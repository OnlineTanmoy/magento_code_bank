<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\MultipleDiscounts\Model\ResourceModel\DiscountMap\CollectionFactory;

/**
 * Class Assigncustomers
 */
class Assigncustomers extends \Magento\Backend\App\Action
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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory
     */
    public $discountMapFactory;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\MultipleDiscounts\Model\DiscountMapFactory $discountMapFactory
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
            $multipleDiscountCollection = $this->collectionFactory->create();
            $multipleDiscountCollection->addFieldToFilter('parent_id', $parentId);
            $multipleDiscountCollection->addFieldToFilter('customer_id', $value);
            $output = $multipleDiscountCollection->getData();
            if ($output) {
                $this->messageManager->addError(__('%1 is already assigned', $customerName));
            } else {
                $mapData = [
                    'parent_id' => $parentId,
                    'customer_id' => $value
                ];
                
                $this->map($mapData);
                $this->messageManager->addSuccess(__('%1 has been assigned', $customerName));
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
    private function map($data)
    {
        $model = $this->discountMapFactory->create();
        $model->setData($data);
        $model->save();
    }
}