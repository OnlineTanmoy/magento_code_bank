<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;
    
    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountFactory
     */
    public $multipleDiscountFactory;
    
    /**
     * @var Session
     */
    public $session;
    
    /**
     * @param Action\Context $context
     * @param Session $session
     * @param \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->multipleDiscountFactory = $multipleDiscountFactory;
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
        $resultPage->setActiveMenu('Appseconnect_MultipleDiscounts::multiple_discounts_manage')
            ->addBreadcrumb(__('Multiple Discounts'), __('Multiple Discounts'))
            ->addBreadcrumb(__('Manage Multiple Discounts'), __('Manage Multiple Discounts'));
        return $resultPage;
    }

    /**
     * Edit Blog Post
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->multipleDiscountFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This discount price no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->session->getFormData(true);
        
        if ($data) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_multiplediscount', $model);
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ?
            __('Edit Discount Price') :
            __('New Discount Price'), $id ?
            __('Edit Discount Price') :
            __('New Discount Price'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Multiple Discounts'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend($model->getId() ?
                'Edit Discount Price' :
                __('New Discount Price'));
        
        return $resultPage;
    }
}