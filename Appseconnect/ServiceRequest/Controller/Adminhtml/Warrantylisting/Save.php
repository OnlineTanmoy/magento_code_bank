<?php

namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Warrantylisting;

use Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterfaceFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductRepository;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Indexer\Model\Processor
     */
    public $processor;

    /**
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * @var \Appseconnect\B2BMage\Model\PricelistRepository
     */
    public $pricelistRepository;

    /**
     * @var ProductAssignInterfaceFactory
     */
    public $productAssignInterfaceFactory;

    /**
     * @var Session
     */
    public $session;

    /**
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    public $serviceRequestPostFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory
     */
    public $warrantyCollectionFactory;


    public function __construct(
        ProductRepository $productRepository,
        Session $session,
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory $warrantyCollectionFactory,
        \Appseconnect\ServiceRequest\Model\WarrantyFactory $warrantyFactory,
        \Appseconnect\ServiceRequest\Model\SerialFactory $serialFactory,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->warrantyFactory = $warrantyFactory;
        $this->filesystem = $filesystem;
        $this->customerFactory = $customerFactory;
        $this->warrantyCollectionFactory = $warrantyCollectionFactory;
        parent::__construct($context);
        $this->processor = $processor;
        $this->date = $date;
        $this->helperServiceEmail = $helperServiceEmail;
        $this->scopeConfig = $scopeConfig;
        $this->serialFactory = $serialFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $registerProductModel = $this->warrantyFactory->create();
//
            if (isset($data['id']) && $id = $data['id']) {
                $registerProductModel = $registerProductModel->load($id);
                $registerProductModel->setData('is_active', $data['is_active']);

                if(is_null($registerProductModel->getData('warranty_start_date'))) { // only for first time
                    $serialCollection = $this->serialFactory->create()->getCollection();
                    $serialObject = $serialCollection
                        ->addFieldToFilter('serial_no', $registerProductModel->getData('mfr_serial_no'))
                        ->getFirstItem();
                    $registerProductModel->setData('warranty_start_date', $registerProductModel->getData('date_of_purchase'));
                    $registerProductModel->setData('warranty_end_date',
                        date('Y-m-d 23:59:59', strtotime("+" . $serialObject->getData('warranty_months') . " months", strtotime($registerProductModel->getData('date_of_purchase')))));
                }

                $registerProductModel->save();
                $this->messageManager->addSuccess(($data['is_active']?__('Warranty product is Active now'):"Warranty product is In-active"));
            } else {
                $id = '';
            }
//
        }
        $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
        if ($returnToEdit) {
            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()
                    ->getParam('id')
            ]);
        } else {
            return $resultRedirect->setPath('*/*/');
        }
    }
}
