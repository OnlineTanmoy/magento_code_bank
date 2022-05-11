<?php

namespace Appseconnect\ServiceRequest\Controller\Warranty;

use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;
use Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory as WarrantyCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Description
 * @package Appseconnect\ServiceRequest\Controller\Request
 */
class Check extends \Magento\Framework\App\Action\Action
{

    /**
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var WarrantyCollectionFactory
     */
    protected $warrantyCollectionFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultFactory;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    protected $contactPersonHelper;

    /**
     * Description constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param WarrantyCollectionFactory $warrantyCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        WarrantyCollectionFactory $warrantyCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Session $customerSession,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory $repairCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
    )
    {
        $this->warrantyCollectionFactory = $warrantyCollectionFactory;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->resultFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->resources = $resourceConnection;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->repairCollectionFactory = $repairCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        // clear any previous warranty validation
        $this->customerSession->unsIsInWarranty();
        $this->customerSession->unsMfrSerial();
        $this->customerSession->unsSku();
        $this->customerSession->unsProductName();
        $this->customerSession->unsDeviceType();
        $this->customerSession->unsRepairPrice();

        $resultRedirect = $this->resultRedirectFactory->create();
        $customerSessionId = $this->customerSession->getCustomerId();
        if (!($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied...'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }


        $validateData = $this->getRequest()->getParams();

        $serialNo = null;
        $modelNo = null;

        if (isset($validateData['serial_number']) && !empty($validateData['serial_number'])) {
            $serialNo = $validateData['serial_number'];
        }
        if (isset($validateData['model_number']) && !empty($validateData['model_number'])) {
            $modelNo = $validateData['model_number'];
        }

        // Get repair cost based on SKU
        $repairCollection = $this->repairCollectionFactory->create();
        $repairCollection->addFieldToFilter('sku', ['eq' => $modelNo]);
        $repaireItem = $repairCollection->getFirstItem()->getData();
        unset($repairCollection);
        $repairPrice = null;
        $sku = "service";
        if($repaireItem) {
            $repairPrice = $repaireItem['repair_cost'];
        }

        // Get warranty detail

        $warrantyCollection = $this->warrantyCollectionFactory->create();
        $customer = $this->customerFactory->create()->load( $customerSessionId );
        if ($this->contactPersonHelper->isContactPerson( $customer )){
            $customerData = $this->contactPersonHelper->getCustomerId( $customerSessionId );
            $customerSessionId=$customerData['customer_id'];
        }
        $warrantyCollection = $this->filterByCustomerId($warrantyCollection, $customerSessionId);
        $warrantyCollection
            ->addFieldToFilter('mfr_serial_no', ['eq' => $serialNo])
            ->addFieldToFilter('sku', ['eq' => $modelNo]);


        $warrantyCollection
            ->addFieldToFilter('is_active', 1);

        $warrantyItem = $warrantyCollection->getFirstItem()->getData();
        unset($warrantyCollection);
        $customerWarrantyData['sku'] = $modelNo;
        $customerWarrantyData['serial_no'] = $serialNo;
        $customerWarrantyData['FPR'] = $repairPrice;

        if (empty($warrantyItem)) { // if item not found in warranty table empty return from here
            $customerWarrantyData['eq_status'] = 'terminated';
            $customerWarrantyData['message'] = 'Unable to identify your device.';

        } else { // check the warranty date
            $customerWarrantyData['eq_status'] = 'active';
            $now = time();
            $endDate = strtotime($warrantyItem['warranty_end_date']);
            $dateDiff = $endDate - $now;
            $inWarranty = false;
            if ($dateDiff >= 0) { // product in warranty
                $inWarranty = true;
            } else { // product not in warranty
                $inWarranty = false; // “You device is out of warranty”
            }
            $customerWarrantyData['is in warranty'] = $inWarranty ? "Yes" : "No";

//            if (in_array($warrantyItem['contract_status'], ["active", "a"])) {
//                $customerWarrantyData['contract_status'] = 'active';
                if ($inWarranty == true) {
                    $customerWarrantyData['message'] = 'You are in warranty';
                } elseif ($inWarranty == false) {
                    $customerWarrantyData['message'] = 'Out of warranty go with FPR';
                }

            $this->customerSession->setMfrSerial($warrantyItem['mfr_serial_no']);
            $this->customerSession->setSku($warrantyItem['sku']);
            $this->customerSession->setProductName($warrantyItem['product_name']);
            $this->customerSession->setDeviceType($validateData['devicetype']);
            $this->customerSession->setRepairPrice($repairPrice);

        }
        $this->customerSession->setIsInWarranty($customerWarrantyData);
        $resultRedirect->setPath('servicerequest/warranty/warrantystatus');
        return $resultRedirect;

    }


    /**
     * @param $collection
     * @param $customerId
     * @return array
     */
    private function getFilteredCollection($collection, $customerId)
    {
        $serialNo = $this->getRequest()->getParam('productSerial');
        $modelNo = $this->getRequest()->getParam('modelNo');
        $collection = $this->filterByCustomerId($collection, $customerId);
        if (!empty($serialNo) && !empty($modelNo)) {
            $collection->addFieldToFilter('sku', ['eq' => $modelNo]);
            $collection->addFieldToFilter('mfr_serial_no', ['eq' => $serialNo]);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getData();
            }
            return null;
        } else if (!empty($serialNo)) {
            $collection->addFieldToFilter('mfr_serial_no', ['eq' => $serialNo]);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getData();
            }
            return null;
        }
        return null;
    }

    public function filterByCustomerId($collection, $customerId)
    {
//        $companyId = $this->contactPersonHelper->getContactCustomerId($customerId);
//        if (!$companyId) {
            $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
//        } else {
//            $collection->addFieldToFilter('customer_id', ['eq' => $companyId]);
//        }
        return $collection;
    }
}
