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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Pricelist;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Appseconnect\B2BMage\Model\PriceFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

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
     * Price list collection
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * Pricelist price
     *
     * @var PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * MassDisable constructor.
     *
     * @param Context           $context               context
     * @param PriceFactory      $pricelistPriceFactory pricelist price
     * @param Filter            $filter                filter
     * @param CollectionFactory $collectionFactory     collection
     */
    public function __construct(
        Context $context,
        PriceFactory $pricelistPriceFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->filter = $filter;
        $this->pricelistPriceFactory = $pricelistPriceFactory;
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
            $pricelistPriceCollection = $this->collectionFactory->create();
            $deletedCount = count($pricelistPriceCollection);
            
            foreach ($pricelistPriceCollection as $pricelist) {
                $this->_bulkDisable($pricelist->getId());
            }
        } elseif ($this->getRequest()->getParam('selected') && $selectedIds) {
            $deletedCount = count($selectedIds);
            foreach ($selectedIds as $pricelistId) {
                $this->_bulkDisable($pricelistId);
            }
        }
        
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $deletedCount));
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Disable pricelist
     *
     * @param int $id id
     *
     * @return void
     */
    private function _bulkDisable($id)
    {
        $model = $this->pricelistPriceFactory->create()->load($id);
        $model->setIsActive(0);
        $model->save();
    }
}
