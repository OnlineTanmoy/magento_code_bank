<?php
namespace Appseconnect\B2BMage\Controller\Customer;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Register extends Action\Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var Session
     */
    public $customerSession;


    /**
     *  @param ScopeConfigInterface $scopeConfig
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Session $customerSession,
        Action\Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig

    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * Order view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->session->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Create New B2B Customer Account'));
        $block = $resultPage->getLayout()->getBlock('B2B.customer.form.register');
        $blockurl = $this->scopeConfig->getValue(
            'insync_b2baccount/createb2b/types',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        if($blockurl){
            return $resultPage;
        }else{
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');

        }
    }
}
