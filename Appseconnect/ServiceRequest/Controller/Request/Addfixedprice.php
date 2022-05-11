<?php

namespace Appseconnect\ServiceRequest\Controller\Request;

use Appseconnect\ServiceRequest\Model\RequestPostFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;


/**
 * Class Submit
 * @package Appseconnect\ServiceRequest\Controller\Request
 */
class Addfixedprice extends \Magento\Framework\App\Action\Action
{
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Appseconnect\ServiceRequest\Helper\ServiceRequest\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    public $cartRep;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    public $formKey;

    /**
     * Submit constructor.
     * @param Context $context
     * @param RequestPostFactory $requestpost
     * @param ResultFactory $result
     * @param ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        RequestPostFactory $requestpost,
        ResultFactory $result,
        ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Data $helperData,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Model\Product $product
    )
    {
        parent::__construct($context);
        $this->_post = $requestpost;
        $this->resultRedirect = $result;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->productRepository = $productRepository;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->helperData = $helperData;
        $this->cart = $cart;
        $this->product = $product;
        $this->formKey = $formKey;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $post = $this->getRequest()->getPostValue();

        $model = $this->_post->create()->load($post['service_id']);

        $filePath = $this->uploadFile();
        if (isset($post['purchase_order_number'])) $model->setData('purchase_order_number', $post['purchase_order_number']);
        if (isset($filePath)) $model->setData('purchase_order_file', $filePath);


        if ($model->save()) {
            if ($this->customerSession->getService() != '') {
                $this->customerSession->setServiceId($this->customerSession->getService());
            } else {
                $this->customerSession->setServiceId($post['service_id']);
            }

            //Load the product based on productID
            $_product = $this->product->loadByAttribute('sku', $post['sku']);
            $params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product' => $_product->getId(), //product Id
                'qty' => 1 //quantity of product
            );
            $this->cart->addProduct($_product, $params);
            $this->cart->save();

            // clear session values
            $this->customerSession->unsWarranty();
            $this->customerSession->unsService();
            $this->customerSession->unsIsInWarranty();
            $this->customerSession->unsMfrSerial();
            $this->customerSession->unsCopackSerial();
            $this->customerSession->unsSku();
            $this->customerSession->unsProductName();

            $resultRedirect->setPath('checkout');

            return $resultRedirect;
        } else {
            $this->messageManager->addErrorMessage(__('The service request not apply.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }


    public function uploadFile()
    {
        if (isset($_FILES['purchase_order_file']['name']) && $_FILES['purchase_order_file']['name'] != '') {
            $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'purchase_order_file']);
            //$uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploaderFactory->setAllowRenameFiles(true);

            $filenameArray = explode('.', $_FILES['purchase_order_file']['name']);
            $ext = end($filenameArray);
            $file_name = 'POF' . $_REQUEST['service_id'] . '.' . $ext;

            //$uploaderFactory->setFilesDispersion(false);
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $fileStorePath = $this->helperData->getGeneralConfig('display_text');
            $destinationPath = $mediaDirectory->getAbsolutePath($fileStorePath);
            $result = $uploaderFactory->save($destinationPath, $file_name);
            $imagePath = $fileStorePath . '/' . $result['file'];
            $post['file_path'] = $imagePath;
            return $post['file_path'];
        }
        return null;
    }

}

