<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\ServiceRequest\Model\RequestPostFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{

    /**
     * @var RequestPostFactory
     */
    public $servicerequestFactory;
    /**
     * @param Context $context
     * @param PriceFactory $pricelistPriceFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        RequestPostFactory $servicerequestFactory
    ) {

        $this->servicerequestFactory = $servicerequestFactory;
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
        $selectedIds = $this->getRequest()->getParam('selected');
        $excludedStatus = $this->getRequest()->getParam('excluded');
        if ($this->getRequest()->getParam('excluded') && $excludedStatus == 'false') {
            $serviceRequestCollection = $this->servicerequestFactory->create()->getCollection();
            $deletedCount = count($serviceRequestCollection);
            
            foreach ($serviceRequestCollection as $serviceRequest) {
                $this->unmap($serviceRequest->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $serviceRequestId) {
                $this->unmap($serviceRequestId);
            }
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $deletedCount));
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    private function unmap($id)
    {
        $this->servicerequestFactory->create()
            ->load($id)
            ->delete();
    }
}
