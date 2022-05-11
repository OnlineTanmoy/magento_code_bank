<?php

namespace Appseconnect\ServiceRequest\Controller\Request;

use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;
use Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory as WarrantyCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Description
 * @package Appseconnect\ServiceRequest\Controller\Request
 */
class Description extends \Magento\Framework\App\Action\Action
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
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $customerSessionId = $this->customerSession->getCustomerId();
        $primaryCustomerId = $this->contactPersonHelper->getContactCustomerId($customerSessionId);
        if (!($primaryCustomerId || $customerSessionId)) {
            $this->messageManager->addError(__('Access Denied...'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $modelNumber = $this->getRequest()->getParam('productModel');
        $validateData = $this->getRequest()->getParams();
        $productDetail = [];
        $output = [];

        // if Serial no. found pass matching SKU
        if (!empty($modelNumber)) {
            $warrantyCollection = $this->warrantyCollectionFactory->create();
            $warrantyCollection = $this->filterByCustomerId($warrantyCollection, $primaryCustomerId);
            $warrantyCollection
                ->addFieldToSelect(array('product_name','sku'))
                ->addFieldToFilter('sku', array('like' => '%' . $modelNumber . '%'));

            if (!empty($warrantyCollection)) {
                $skus = [];
                foreach ($warrantyCollection as $warrantyData) {
                    if (isset($skus[$warrantyData->getSku()])) {
                        continue;
                    }
                    $productDetail['id'] = json_encode([
                        'description' => $warrantyData->getProductName(),
                        'model_number' => $warrantyData->getSku()
                    ]);
                    $productDetail['text'] = $warrantyData->getSku();
                    $output[] = $productDetail;
                    $skus[$warrantyData->getSku()] = 1;
                }
            }

        // if Part No. / SKU required
        } elseif (!empty($this->getRequest()->getParam('productSerial'))) {
            $collection = $this->warrantyCollectionFactory->create();
            $data = $this->getFilteredCollection($collection, $primaryCustomerId);
            $isRequired = 0;
            if (!empty($data)) {
                if (isset($data['mfr_serial_no']) && !empty($data['copack_serial_no'])) {
                    $isRequired = 1;
                } else if (isset($data['mfr_serial_no']) && empty($data['copack_serial_no'])) {
                    $isRequired = 2;
                } else {
                    $isRequired = 0;
                }
            }
            $output = ['isRequired' => $isRequired];

        } elseif (!empty($validateData)) {
            $serialNo = null;
            $modelNo = null;
            $coPackNo = null;

            if (isset($validateData['serial_number']) && !empty($validateData['serial_number'])) {
                $serialNo = $validateData['serial_number'];
            }
            if (isset($validateData['copack_serial_number'])) {
                $coPackNo = $validateData['copack_serial_number'];
            }
            if (isset($validateData['product_model']) && !empty($validateData['product_model'])) {
                $modelNo = $validateData['product_model'];
            }

            $isValid = 1;
            $collection = $this->warrantyCollectionFactory->create();
            $collection = $this->filterByCustomerId($collection, $primaryCustomerId);

            if (!empty($serialNo)) {
                $collection->addFieldToFilter('mfr_serial_no', [
                    'eq' => $serialNo
                ]);
            } else {
                $isValid = 0;
            }
            if (!empty($serialNo) && !empty($coPackNo)) {
                $collection->addFieldToFilter('copack_serial_no', ['eq' => $coPackNo]);
            }
            if (empty($serialNo)) {
                $isValid = 0;
            }
            $item = $collection->getFirstItem()->getData();
            $dateDiff = 0;

            if (empty($serialNo) || !isset($item['mfr_serial_no']) || $item['mfr_serial_no'] != $serialNo || (!empty($item['copack_serial_number']) && isset($coPackNo) && $item['copack_serial_number'] != $coPackNo) || $item['sku'] != $modelNo) {
                $isValid = 0;
            } else {
                $now = time();
                $endDate = strtotime($item['warranty_end_date']);
                $dateDiff = $endDate - $now;
            }

            // Validate Model Number
            if (empty($modelNo)) {
                $isValid = 0;
            } else {
                $product = $this->productRepository->get($modelNo);
                if (empty($product) || $product->getSku() != $modelNo) {
                    $isValid = 0;
                }
            }

            $output = json_encode([
                'isValid' => $isValid,
                'isEnded' => $dateDiff > 0 ? 0 : 1,
                'message' => $isValid ? __('Validate Successfully') : __('No matching record found, Please contact our service department')
            ]);

            return $resultJson->setData($output);
        }

        return $resultJson->setData($output);
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
            $collection->addFieldToFilter('sku', [
                'eq' => $modelNo
            ]);
            $collection->addFieldToFilter('mfr_serial_no', [
                'eq' => $serialNo
            ]);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getData();
            }
            return null;
        } else if (!empty($serialNo)) {
            $collection->addFieldToFilter('mfr_serial_no', [
                'eq' => $serialNo
            ]);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getData();
            }
            return null;
        }
        return null;
    }

    public function filterByCustomerId($collection, $customerId)
    {
        $companyId = $this->contactPersonHelper->getContactCustomerId($customerId);
        if (!$companyId) {
            $collection->addFieldToFilter('customer_id', [
                'eq' => $customerId
            ]);
        } else {
            $collection->addFieldToFilter('customer_id', [
                'eq' => $companyId
            ]);
        }
        return $collection;
    }


}
