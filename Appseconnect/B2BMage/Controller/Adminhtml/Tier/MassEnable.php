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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Tier;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class MassDelete
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class MassEnable extends \Magento\Backend\App\Action
{
    
    /**
     * Filter
     *
     * @var Filter
     */
    public $filter;
    
    /**
     * Tier price collection
     *
     * @var CollectionFactory
     */
    public $collectionFactory;
    
    /**
     * Customer tier price
     *
     * @var \Appseconnect\B2BMage\Model\ProductFactory
     */
    public $customerTierPriceFactory;
    
    /**
     * Mass enable contractor
     *
     * @param Context                                    $context                  context
     * @param \Appseconnect\B2BMage\Model\ProductFactory $customerTierPriceFactory customer tier price
     * @param Filter                                     $filter                   filter
     * @param CollectionFactory                          $collectionFactory        tier price collection
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Model\ProductFactory $customerTierPriceFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->filter = $filter;
        $this->customerTierPriceFactory = $customerTierPriceFactory;
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
            $customerTierPriceCollection = $this->collectionFactory->create();
            $deletedCount = count($customerTierPriceCollection);
            
            foreach ($customerTierPriceCollection as $customerTierPrice) {
                $this->_bulkEnable($customerTierPrice->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $customerTierPriceId) {
                $this->_bulkEnable($customerTierPriceId);
            }
        }
        
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been enabled.', $deletedCount)
        );
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * Bulk enable
     *
     * @param int $id Tier price id
     *
     * @return void
     */
    private function _bulkEnable($id)
    {
        $model = $this->customerTierPriceFactory->create()->load($id);
        $model->setIsActive(1);
        $model->save();
    }
}
