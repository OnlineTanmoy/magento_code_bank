<?php

namespace Appseconnect\ServiceRequest\Controller\Request;
class Search extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Appseconnect\ServiceRequest\Helper\Search
     */
    protected $searchHelper;

    /**
     * Search constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Appseconnect\ServiceRequest\Helper\Search $searchHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Appseconnect\ServiceRequest\Helper\Search $searchHelper,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->searchHelper = $searchHelper;
        $this->_customerSession = $customerSession;
        $this->_pageFactory = $pageFactory;
        $this->_coreRegistry = $coreRegistry;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!($customerSessionId = $this->_customerSession->getCustomerId())) {
            $this->messageManager->addError(__('Access Denied...'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        $updateAction = $this->getRequest()->getParams();
        $resultFactory = $this->_objectManager->get(\Magento\Framework\Controller\ResultFactory::class);
        $resultFactory = $resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        if ($updateAction) {
            unset($updateAction['form_key']);
            if (isset($updateAction['request_status']) && !$updateAction['request_status']) {
                unset($updateAction['request_status']);
            }
            if (isset($updateAction['from_date']) && !$updateAction['from_date']) {
                unset($updateAction['from_date']);
            }
            if (isset($updateAction['to_date']) && !$updateAction['to_date']) {
                unset($updateAction['to_date']);
            }
        }
        $searchData = (!empty($updateAction) ? $updateAction : false);
        $this->searchHelper->getSearchData($searchData, true);
        $this->_coreRegistry->register('search_data', $searchData);

        $htmlElement = 'main';

        $view = $this->_view->loadLayout('servicerequest_request_listing');
        $layout = $view->getLayout()->getBlock('request.list.block');
        $updateActions = $layout->toHtml();

        $resultFactory->setData(['content' => $updateActions, 'success' => true]);

        return $resultFactory;
    }
}
