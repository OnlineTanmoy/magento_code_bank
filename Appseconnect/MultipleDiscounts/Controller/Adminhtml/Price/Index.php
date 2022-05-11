<?php
namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_MultipleDiscounts::multiple_discounts_manage');
        $resultPage->addBreadcrumb(__('Manage Multiple Discounts'), __('Manage Multiple Discounts'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Manage Multiple Discounts'));

        return $resultPage;
    }
}