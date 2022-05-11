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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Salesrep;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\B2BMage\Model\SalesrepgridFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Salesrepgrid\CollectionFactory;

/**
 * Class MassDisable
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class MassDisable extends \Magento\Backend\App\Action
{
    
    /**
     * Filter
     *
     * @var Filter
     */
    public $filter;
    
    /**
     * Salesrep collection
     *
     * @var CollectionFactory
     */
    public $collectionFactory;
    
    /**
     * Salesrep model
     *
     * @var SalesrepgridFactory
     */
    public $salesRepFactory;
    
    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * MassDisable constructor.
     *
     * @param Context                                 $context           context
     * @param SalesrepgridFactory                     $salesRepFactory   salesrep model
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory   customer model
     * @param Filter                                  $filter            filter
     * @param CollectionFactory                       $collectionFactory salesrep collection
     */
    public function __construct(
        Context $context,
        SalesrepgridFactory $salesRepFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->salesRepFactory = $salesRepFactory;
        $this->customerFactory = $customerFactory;
        $this->filter = $filter;
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
            $salesRepCollection = $this->collectionFactory->create();
            $deletedCount = count($salesRepCollection);
            
            foreach ($salesRepCollection as $salesRep) {
                $this->_bulkDisable($salesRep->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $salesRepId) {
                $this->_bulkDisable($salesRepId);
            }
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $deletedCount));
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * Bulk desable
     *
     * @param int $id salesrep id
     *
     * @return void
     */
    private function _bulkDisable($id)
    {
        $model = $this->salesRepFactory->create()->load($id);
        $salesRepCusomerId = $model->getSalesrepCustomerId();
        $model->setIsActive(0);
        $model->save(1);
        $customer = $this->customerFactory->create()->load($salesRepCusomerId);
        $customer->setCustomerStatus(0);
        $customer->save();
    }
}
