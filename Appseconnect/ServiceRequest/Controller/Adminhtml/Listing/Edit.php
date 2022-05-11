<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing;

use Magento\Backend\App\Action;
use Appseconnect\ServiceRequest\Model\RequestPostFactory;
use Magento\Backend\Model\Session;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var PriceFactory
     */
    public $requestPostFactory;

    /**
     * @var Session
     */
    public $session;

    /**
     * @param Action\Context $context
     * @param PriceFactory $pricelistPriceFactory
     * @param Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        RequestPostFactory $requestPostFactory,
        Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {

        $this->requestPostFactory = $requestPostFactory;
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_ServiceRequest::servicerequest')
            ->addBreadcrumb(__('Service Request'), __('Service Request'))
            ->addBreadcrumb(__('Manage Service Request'), __('Manage Service Request'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->requestPostFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This post no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $data = $this->session->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_servicerequest', $model);
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ?
            __('Edit Service Request') :
            __('New Service Request'), $id ?
            __('Edit Service Request') :
            __('New Service Request'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Service Request'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend($model->getId() ?
                "#" . $model->getRaId() :
                __('New Service Request'));
        
        return $resultPage;
    }
}
