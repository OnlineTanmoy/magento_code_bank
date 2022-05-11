<?php

namespace Appseconnect\Shoppinglist\Controller\Customer\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;


class Editlist extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @var PageFactory
     */
    /**
     * @var Session
     */
    public $customerSession;
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context              $context,
        PageFactory          $resultPageFactory,
        ScopeConfigInterface $scopeConfig

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;


        parent::__construct( $context );
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set( __( 'Edit List' ) );

        $block = $resultPage->getLayout()->getBlock( 'customer.account.link.back' );
        $blockurl = $this->scopeConfig->getValue(
            'insync_shoppinglist/general/enable_shoppinglist_active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($block) {
            $block->setRefererUrl( $this->_redirect->getRefererUrl() );
        }
        if ($blockurl) {
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath( 'customer/account' );
        }
    }
}
