<?php

namespace Appseconnect\ServiceRequest\Controller\Request;

use Appseconnect\ServiceRequest\Model\RequestPostFactory;
use Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory as WarrantyCollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;


/**
 * Class Submit
 * @package Appseconnect\ServiceRequest\Controller\Request
 */
class Submit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $_pageFactory;
    /**
     * @var RequestPostFactory
     */
    protected $_post;
    /**
     * @var ResultFactory
     */
    protected $resultRedirect;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;
    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;
    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected $helperData;

    /**
     * @var \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory
     */
    public $repairCollectionFactory;

    /**
     * Submit constructor.
     * @param Context $context
     * @param RequestPostFactory $requestpost
     * @param ResultFactory $result
     * @param ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param WarrantyCollectionFactory $warrantyCollectionFactory
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
     * @param \Appseconnect\ServiceRequest\Helper\ServiceRequest\Data $helperData
     * @param \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory $repairCollectionFactory
     * @param \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail
     */
    public function __construct(
        Context                                                                   $context,
        RequestPostFactory                                                        $requestpost,
        ResultFactory                                                             $result,
        ManagerInterface                                                          $messageManager,
        \Magento\Customer\Model\Session                                           $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime                               $date,
        \Magento\Framework\View\Result\PageFactory                                $pageFactory,
        \Magento\Framework\UrlInterface                                           $url,
        WarrantyCollectionFactory                                                 $warrantyCollectionFactory,
        UploaderFactory                                                           $uploaderFactory,
        AdapterFactory                                                            $adapterFactory,
        Filesystem                                                                $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface                        $scopeConfig,
        \Magento\Customer\Model\CustomerFactory                                   $customerFactory,
        \Magento\Sales\Model\OrderNotifier                                        $emailNotifier,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data                           $contactPersonHelper,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Data                   $helperData,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory $repairCollectionFactory,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email                  $helperServiceEmail
    )
    {
        parent::__construct( $context );
        $this->_post = $requestpost;
        $this->resultRedirect = $result;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->_pageFactory = $pageFactory;
        $this->warrantyCollectionFactory = $warrantyCollectionFactory;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->helperData = $helperData;
        $this->url = $url;
        $this->repairCollectionFactory = $repairCollectionFactory;
        $this->date = $date;
        $this->helperServiceEmail = $helperServiceEmail;
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->emailNotifier = $emailNotifier;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl( $this->_redirect->getRefererUrl() );
        $post = $this->getRequest()->getPostValue();
        $currentCustomerInSession = $this->customerSession->getCustomer();

        if (!isset( $post['serial_number'] ) || !isset( $post['model_number'] )) {
            $this->messageManager->addErrorMessage( __( 'Please provide the required information and try again.' ) );
            return $resultRedirect;
        }

        // When customer approve the new Service cost assign by Admin
        if (isset( $post['customer_accept'] ) && $post['customer_accept'] == 1) {
            $serviceRequestModel = $this->_post->create()->load( $post['request_service_id'] );
            $order = $this->helperData->createOrder( "service", $serviceRequestModel->getFprPrice(), $serviceRequestModel );
            $serviceRequestModel->setData( 'service_quote_required', 0 )->save();
            $this->messageManager->addSuccessMessage( __( 'You have submitted the service request [' ) . $serviceRequestModel->getRaId() . '] with the update amount.' );
            return $resultRedirect;
        }

        // get YEAR prefix
        $datePrefix = date( "y" );
        $serviceNumber = intval( $datePrefix ) * 10 + 5;
        $requestServiceId = $post['request_service_id'];
        $serviceRequestModel = null;
        $primaryCustomerId = null;
        if ($requestServiceId) {
            $serviceRequestModel = $this->_post->create()->load( $post['request_service_id'] );
            $primaryCustomerId = $serviceRequestModel->getData( 'customer_id' );
        } else {
            $serviceRequestModel = $this->_post->create();
            $customerId = $currentCustomerInSession->getId();
            $storeId = $currentCustomerInSession->getStoreId();
            $customer = $this->customerFactory->create()->load( $customerId );

            if ($this->contactPersonHelper->isContactPerson( $customer )) {
                // if B2B Customer
                $primaryCustomerId = $this->contactPersonHelper->getContactCustomerId( $customerId );
            } else {
                $primaryCustomerId = $customerId;
            }
            $serviceRequestModel->setData( 'customer_id', $primaryCustomerId );
            $serviceRequestModel->setData( 'customer_name', $this->contactPersonHelper->getCustomerNameById( $primaryCustomerId ) );
            $serviceRequestModel->setData( 'contact_person_id', $customerId );
            $serviceRequestModel->setData( 'store_id', $storeId );
        }

        $serviceRequestModel->setData( 'model_number', $post['model_number'] );
        $serviceRequestModel->setData( 'fpr_price', $post['fpr_price'] );
        $serviceRequestModel->setData( 'serial_number', $post['serial_number'] );
        $serviceRequestModel->setData( 'short_description', $post['short_description'] );
        $serviceRequestModel->setData( 'detailed_description', $post['detailed_description'] );
        if (isset( $post['safety1'] )) $serviceRequestModel->setData( 'safety1', $post['safety1'] );
        if (isset( $post['safety2'] )) $serviceRequestModel->setData( 'safety2', $post['safety2'] );
        if (isset( $post['safety3'] )) $serviceRequestModel->setData( 'safety3', $post['safety3'] );
        $serviceRequestModel->setData( 'terms_condition', isset( $post['terms_condition'] ) ? $post['terms_condition'] : '' );
        $serviceRequestModel->setData( 'is_warranty', $post['is_warranty'] );
        $serviceRequestModel->setData( 'device_type', $post['device_type'] );
        if (isset( $post['shipping_address_id'] )) $serviceRequestModel->setData( 'shipping_address_id', $post['shipping_address_id'] );
        if (isset( $post['purchase_order_number'] )) {
            $serviceRequestModel->setData( 'purchase_order_number', $post['purchase_order_number'] );
        }

        // supporting doc
        $filePath = $this->uploadFile( 'file_path' );
        if ($filePath) $serviceRequestModel->setData( 'file_path', $filePath );

        // po doc
        $purchaseOrderFile = $this->uploadFile( 'purchase_order_file' );
        if ($purchaseOrderFile) $serviceRequestModel->setData( 'purchase_order_file', $purchaseOrderFile );

        if ($post['requestStatus'] == 'draft') {
            if (is_null( $serviceRequestModel->getData( 'draft_date' ) )) {
                $serviceRequestModel->setData( 'draft_date', $this->date->gmtDate() );
            }
            $serviceRequestModel->setData( 'status', 1 )->save();

            // clear session values
            $this->customerSession->unsIsInWarranty();
            $this->customerSession->unsMfrSerial();
            $this->customerSession->unsCopackSerial();
            $this->customerSession->unsSku();
            $this->customerSession->unsProductName();
        } else {
            if (is_null( $serviceRequestModel->getData( 'draft_date' ) )) {
                $serviceRequestModel->setData( 'draft_date', $this->date->gmtDate() );
            }
            $serviceRequestModel->setData( 'submit_date', $this->date->gmtDate() );
            $serviceRequestModel->setData( 'status', 2 )->save();
        }

        if ($serviceRequestModel->save()) {

            // Set ra_id based on service type
            if ($post['requestStatus'] == 'draft') {
                $raID = $serviceRequestModel->getData( 'ra_id' );
                if (is_null( $raID ) || empty( $raID )) {
                    $lastDraftNumber = intval( $this->helperData->getLastNumber( 'insync/service/lastdraft' ) );
                    $lastDraftNumber += 1;
                    $serviceRequestModel->setData( 'ra_id', 'D' . ((pow( 10, 8 ) * $serviceNumber) + $lastDraftNumber) );
                    $serviceRequestModel->save();
                    $this->helperData->setLastNumber( 'insync/service/lastdraft', $lastDraftNumber, 0 );
                    $raID = $serviceRequestModel->getData( 'ra_id' );
                }
                $this->messageManager->addSuccessMessage( __( 'You have saved the service request as draft. [' . $raID . ']' ) );
            } else {
                $lastServiceNumber = intval( $this->helperData->getLastNumber( 'insync/service/lastservice' ) );
                $lastServiceNumber += 1;
                $serviceRequestModel->setData( 'ra_id', ((pow( 10, 9 ) * $serviceNumber) + $lastServiceNumber) );
                $serviceRequestModel->save();
                $this->helperData->setLastNumber( 'insync/service/lastservice', $lastServiceNumber, 0 );
            }

            // check warranty
            // only apply where status in (active, onhold, draft)
            $collection = $this->warrantyCollectionFactory->create();
            $collection
                ->addFieldToFilter( 'mfr_serial_no', ['eq' => $post['serial_number']] )
                ->addFieldToFilter( 'customer_id', ['eq' => $primaryCustomerId] )
                ->addFieldToFilter( 'is_active', ['eq' => 1] );

            $item = $collection->getFirstItem()->getData();
            $now = time();
            $endDate = strtotime( $item['warranty_end_date'] );
            $dateDiff = $endDate - $now;
            $isWarranty = $dateDiff > 0 ? 1 : 0;
            $serviceRequestModel->setData( 'is_warranty', $isWarranty )->save();

            // if not draft place the order
            if ($post['requestStatus'] != 'draft') {
                $collection = $this->repairCollectionFactory->create();
                $collection->addFieldToFilter( 'sku', [
                    'eq' => $post['model_number']
                ] );
                $item = $collection->getFirstItem()->getData();
                $price = 0;
                $sku = "service";
                if ($item) {
                    $price = $item['repair_cost'];
                }

                $order = null;
                // place order only when service is not in warranty and price is found
                if ($isWarranty == 0 && $price) {
                    $order = $this->helperData->createOrder( $sku, $price, $serviceRequestModel );
                }
                if ($isWarranty == 0 && $price == 0) {
                    $serviceRequestModel->setData( 'fpr_price', 0 );
                    $serviceRequestModel->setData( 'service_quote_required', 1 );
                    $this->messageManager->addNoticeMessage( __( 'Once the Repair cost is quoted you would need to accept and confirm the repairing.' ) );
                }

                // load primary customer
                $b2bCustomer = $this->customerFactory->create()->load( $primaryCustomerId );
                $b2bCustomerName = $b2bCustomer->getFirstname() . ' ' . $b2bCustomer->getLastname();
                $b2bCustomerEmail = $b2bCustomer->getEmail();
                $billingAddressId = $b2bCustomer->getDefaultBilling();
                if ($billingAddressId) {
                    $serviceRequestModel->setData( 'billing_address_id', $billingAddressId );
                }
                $serviceRequestModel->setData( 'fpr_price', $price )->save();

                // Send to contact person
                $emailTempVariables = [
                    'customer_name' => $b2bCustomerName,
                    'service_number' => $serviceRequestModel->getRaId()
                ];

                $receiverInfo = [
                    'name' => $currentCustomerInSession->getFirstname() . ' ' . $currentCustomerInSession->getLastname(),
                    'email' => $currentCustomerInSession->getEmail()
                ];
                $this->helperServiceEmail->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $receiverInfo,
                    'submited'
                );

                // custom email 2
                $custom2Name = $this->scopeConfig->getValue( 'trans_email/ident_custom2/name', 'store' );
                $custom2Email = $this->scopeConfig->getValue( 'trans_email/ident_custom2/email', 'store' );

                $custom2Info = [
                    'name' => $custom2Name,
                    'email' => $custom2Email
                ];
                $this->helperServiceEmail->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $custom2Info,
                    'submited'
                );

                // send service status mail to BP
                $receiverInfo = [
                    'name' => $b2bCustomerName,
                    'email' => $b2bCustomerEmail
                ];
                $this->helperServiceEmail->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $receiverInfo,
                    'submited'
                );

                $this->messageManager->addSuccessMessage( __( 'You have submitted the service request [' ) . $serviceRequestModel->getRaId() . '].' );
            }

            // clear session values
            $this->customerSession->unsIsInWarranty();
            $this->customerSession->unsMfrSerial();
            $this->customerSession->unsCopackSerial();
            $this->customerSession->unsSku();
            $this->customerSession->unsProductName();

            $resultRedirect->setUrl( $this->url->getUrl( 'servicerequest/form/index', array('id' => $serviceRequestModel->getId()) ) );
            //print_r($resultRedirect->getData());exit;
            return $resultRedirect;
        } else {
            $this->messageManager->addErrorMessage( __( 'The service request not submitted.' ) );
            $resultRedirect->setUrl( $this->_redirect->getRefererUrl() );
            return $resultRedirect;
        }
    }

    /**
     * @param $requiredFields
     * @param $discountData
     * @return int
     */
    protected function checkRequiredFields($requiredFields, $data)
    {
        $isError = 0;
        foreach ($requiredFields as $requiredValues) {
            if (isset( $data[$requiredValues] ) && trim( $data[$requiredValues] ) == '') {
                $isError = 1;
                break;
            }
            if (!isset( $data[$requiredValues] )) {
                $isError = 1;
                break;
            }
        }
        return $isError;
    }

    public function uploadFile($fileField)
    {
        if (isset( $_FILES[$fileField]['name'] ) && $_FILES[$fileField]['name'] != '') {
            $uploaderFactory = $this->uploaderFactory->create( ['fileId' => $fileField] );
            //$uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploaderFactory->setAllowRenameFiles( true );
            //$uploaderFactory->setFilesDispersion(false);
            $mediaDirectory = $this->filesystem->getDirectoryWrite( DirectoryList::MEDIA );
            $fileStorePath = $this->helperData->getGeneralConfig( 'display_text' );
            $destinationPath = $mediaDirectory->getAbsolutePath( $fileStorePath );
            $result = $uploaderFactory->save( $destinationPath );
            $imagePath = $fileStorePath . '/' . $result['file'];
            $post[$fileField] = $imagePath;
            return $post[$fileField];
        } else {
            return null;
        }
    }

}

