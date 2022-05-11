<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Serial;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

class Massactive extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Appseconnect\ServiceRequest\Model\SerialFactory $serialFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->filter = $filter;
        $this->serialFactory = $serialFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $collection = $this->serialFactory->create()->getCollection();
        $collection->addFieldToFilter('id', ['in', $postData['selected']]);
        $collectionSize = 0;
        foreach ($collection as $_eachSerialCollection) {
            $_eachSerialCollection->setData('is_active', 1)->save();
            $collectionSize++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been activated.', $collectionSize));

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
