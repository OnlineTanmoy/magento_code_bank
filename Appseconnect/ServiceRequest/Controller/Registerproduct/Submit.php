<?php

namespace Appseconnect\ServiceRequest\Controller\Registerproduct;

use Appseconnect\ServiceRequest\Model\RegisterproductFactory;
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
     * @var RegisterproductFactory
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

    /**
     * @var \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory
     */
    public $repairCollectionFactory;
    /**
     * contactPersonHelper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;

    /**
     * Submit constructor.
     * @param Context $context
     * @param RegisterproductFactory $newRegisterPost
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
     * @param \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory $repairCollectionFactory
     * @param \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper contactPersonHelper
     */
    public function __construct(
        Context                                                                   $context,
        RegisterproductFactory                                                    $newRegisterPost,
        ResultFactory                                                             $result,
        ManagerInterface                                                          $messageManager,
        \Magento\Customer\Model\Session                                           $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone,
        \Magento\Framework\View\Result\PageFactory                                $pageFactory,
        \Magento\Framework\UrlInterface                                           $url,
        WarrantyCollectionFactory                                                 $warrantyCollectionFactory,
        UploaderFactory                                                           $uploaderFactory,
        AdapterFactory                                                            $adapterFactory,
        Filesystem                                                                $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface                        $scopeConfig,
        \Magento\Customer\Model\CustomerFactory                                   $customerFactory,
        \Magento\Sales\Model\OrderNotifier                                        $emailNotifier,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory $repairCollectionFactory,
        \Appseconnect\ServiceRequest\Model\SerialFactory                          $serialFactory,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email                  $helperServiceEmail,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data                           $contactPersonHelper

    )
    {
        parent::__construct( $context );
        $this->_post = $newRegisterPost;
        $this->resultRedirect = $result;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->_pageFactory = $pageFactory;
        $this->warrantyCollectionFactory = $warrantyCollectionFactory;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->url = $url;
        $this->repairCollectionFactory = $repairCollectionFactory;
        $this->timezone = $timezone;
        $this->helperServiceEmail = $helperServiceEmail;
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->emailNotifier = $emailNotifier;
        $this->serialFactory = $serialFactory;
        $this->contactPersonHelper = $contactPersonHelper;
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
        $post['date_of_purchase'] = str_replace( '/', '-', $post['date_of_purchase'] );


        // get YEAR prefix

        $newRegisterModel = null;

        $newRegisterModel = $this->_post->create();
        $customerId = $currentCustomerInSession->getId();
        $customer = $this->customerFactory->create()->load( $customerId );
        $storeId = $currentCustomerInSession->getStoreId();

        if ($this->contactPersonHelper->isContactPerson( $customer )) {
            $customerData = $this->contactPersonHelper->getCustomerId( $customerId );
            $newRegisterModel->setData( 'customer_id', $customerData['customer_id'] );
        } else {
            $newRegisterModel->setData( 'customer_id', $customerId );
        }
        $newRegisterModel->setData( 'customer_name', $currentCustomerInSession->getFirstname() . ' ' . $currentCustomerInSession->getLastname() );
        $newRegisterModel->setData( 'store_id', $storeId );

        $newRegisterModel->setData( 'sku', $post['model_number'] );
        $newRegisterModel->setData( 'mfr_serial_no', $post['serial_number'] );
        $newRegisterModel->setData( 'terms_condition', isset( $post['terms_condition'] ) ? $post['terms_condition'] : false );
        if ($post['date_of_purchase'] != '') {
            $newRegisterModel->setData( 'date_of_purchase', $this->timezone->date( new \DateTime( $post['date_of_purchase'] ) )->format( 'm/d/y' ) );
        }
        $newRegisterModel->setData( 'purchase_order_number', $post['purchase_order_number'] );

        // look for serial no for inserting warranty start date and end date
        $serialCollection = $this->serialFactory->create()->getCollection();
        $serialObject = $serialCollection
            ->addFieldToFilter( 'serial_no', $post['serial_number'] )
            ->getFirstItem();
        $_serialFound = false;
        $_errorMessage = "Given serial no [" . $post['serial_number'] . "] not found";
        if ($serialObject) {
            if (!$serialObject->getIsActive()) {
                $_errorMessage = "Given serial no [" . $post['serial_number'] . "] is not active";
            } elseif ($serialObject->getData( 'assign_customer' ) != 0) {
                $_errorMessage = "Given serial no [" . $post['serial_number'] . "] already in use";
            } else {
                $_serialFound = true;

                // TODO this should be base on Setting
//                $newRegisterModel->setData('warranty_start_date', date("Y-m-d"));
//                $newRegisterModel->setData('warranty_end_date', date('Y-m-d 23:59:59', strtotime("+" . $serialObject->getData('warranty_months') . " months")));
                if ($this->contactPersonHelper->isContactPerson( $customer )) {
                    $customerData = $this->contactPersonHelper->getCustomerId( $customerId );
                    $serialObject->setData( 'assign_customer', $customerData['customer_id'] );
                    $serialObject->save();
                } else {
                    $serialObject->setData( 'assign_customer', $customerId);
                    $serialObject->save();
                }
            }
        }

        // po doc
        $purchaseOrderFile = $this->uploadFile( 'purchase_order_file' );
        if ($purchaseOrderFile) $newRegisterModel->setData( 'purchase_order_file', $purchaseOrderFile );

//        $newRegisterModel->setData('submit_date', $this->date->gmtDate());

        if ($_serialFound) {
            if ($newRegisterModel->save()) {
                $resultRedirect->setUrl( $this->url->getUrl( 'servicerequest/registerproduct/newproduct', array() ) );
                $this->messageManager->addSuccess( __( "Your product with serial no.[" . $post['serial_number'] . "] register, Waiting for approval" ) );
                return $resultRedirect;
            } else {
                $this->messageManager->addErrorMessage( __( 'Registration fail !!! Try again' ) );
                $resultRedirect->setUrl( $this->_redirect->getRefererUrl() );
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage( __( $_errorMessage ) );
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
            $fileStorePath = $this->scopeConfig->getValue( 'insync_service/service_document/register_po_document_path', 'store' );
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

