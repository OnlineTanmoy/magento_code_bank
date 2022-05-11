<?php

namespace Appseconnect\B2BMage\Controller\Adminhtml\Special;

class SavePricelist extends \Magento\Backend\App\Action
{
    public $resultPageFactory;

    public $resultJsonFactory;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * Price collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerRepository = $customerRepository;
        $this->pricelistFactory = $pricelistFactory;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        $customerId = $post['customer_id'];
        $customer = $this->customerRepository->getById($customerId);

        $pricelistCode = 0;
        if ($customer->getCustomAttribute('pricelist_code') == null){
            $pricelistCode = 0;
        } else {
            $pricelistCode = $customer->getCustomAttribute('pricelist_code')->getValue();
        }

        if ($pricelistCode != 0) {
            $pricelistCollection = $this->pricelistFactory->create()
                ->addFieldToFilter('id', $pricelistCode)
                ->addFieldToSelect('pricelist_name')
                ->getFirstItem()
                ->getData();

            $pricelistName = $pricelistCollection['pricelist_name'];

        } else {
            $pricelistName = "Base Price";
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(["pricelistId" => $pricelistCode, "pricelistName" => $pricelistName]);
        return $resultJson;
    }

}