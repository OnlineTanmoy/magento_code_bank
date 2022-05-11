<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Adminhtml\Special;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class MassDelete
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class MassDelete extends \Magento\Backend\App\Action
{

    /**
     * Filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Special price collection
     *
     * @var CollectionFactory
     */
    public $collectionFactory;
    
    /**
     * Customer special price
     *
     * @var \Appseconnect\B2BMage\Model\CustomerFactory
     */
    public $customerSpecialPriceFactory;

    /**
     * Mass delete constructor
     *
     * @param Context                                     $context                     context
     * @param \Appseconnect\B2BMage\Model\CustomerFactory $customerSpecialPriceFactory customer special price
     * @param Filter                                      $filter                      filter
     * @param CollectionFactory                           $collectionFactory           special price collection
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Model\CustomerFactory $customerSpecialPriceFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->filter = $filter;
        $this->customerSpecialPriceFactory = $customerSpecialPriceFactory;
        $this->collectionFactory = $collectionFactory;
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
            $customerSpecialPriceCollection = $this->collectionFactory->create();
            
            $deletedCount = count($customerSpecialPriceCollection);
            
            foreach ($customerSpecialPriceCollection as $customerSpecialPrice) {
                $this->_unmap($customerSpecialPrice->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $customerSpecialPriceId) {
                $this->_unmap($customerSpecialPriceId);
            }
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $deletedCount));
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Unmap
     *
     * @param int $customerSpecialPriceId customer special price id
     *
     * @return void
     */
    private function _unmap($customerSpecialPriceId)
    {
        $this->customerSpecialPriceFactory->create()
            ->load($customerSpecialPriceId)
            ->delete();
    }
}
