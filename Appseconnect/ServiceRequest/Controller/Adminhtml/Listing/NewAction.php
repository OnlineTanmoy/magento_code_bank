<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing;
use Magento\Backend\App\Action;
use Appseconnect\ServiceRequest\Model\RequestPost as RequestPost;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Edit Service Request Page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();

        $serviceDatas = $this->getRequest()->getParam('service');
        if(is_array($serviceDatas)) {
            $contact = $this->_objectManager->create(RequestPost::class);
            $contact->setData($serviceDatas)->save();
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
