<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Warrantylisting;

use Magento\Backend\App\Action;
use Appseconnect\ServiceRequest\Model\WarrantyFactory;
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
    public $warrantyFactory;

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
        WarrantyFactory $warrantyFactory,
        Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {

        $this->warrantyFactory = $warrantyFactory;
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
        $resultPage->setActiveMenu('Appseconnect_ServiceRequest::warrantylist')
            ->addBreadcrumb(__('Warranty Request'), __('Warranty Request'))
            ->addBreadcrumb(__('Manage Warranty Request'), __('Manage Warranty Request'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->warrantyFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This warranty no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $data = $this->session->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_warrantyrequest', $model);
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ?
            __('Edit Service Request') :
            __('New Service Request'), $id ?
            __('Edit Service Request') :
            __('New Service Request'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Warranty Request'));
//        $resultPage->getConfig()
//            ->getTitle()
//            ->prepend($model->getId() ?
//                "#" . $model->getRaId() :
//                __('New Service Request'));
        
        return $resultPage;
    }
}
