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

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\ProductFactory;

/**
 * Class Delete
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Tier price product
     *
     * @var ProductFactory
     */
    public $tierPriceProductFactory;

    /**
     * Save constructor
     *
     * @param Action\Context $context                 context
     * @param ProductFactory $tierPriceProductFactory Tier price product
     */
    public function __construct(Action\Context $context, ProductFactory $tierPriceProductFactory)
    {
        parent::__construct($context);
        $this->tierPriceProductFactory = $tierPriceProductFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $tierPriceModel = $this->tierPriceProductFactory->create();
                $tierPriceModel->load($id);
                $tierPriceModel->delete();
                $this->messageManager->addSuccess(__('The Customer Tier Price has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit', [
                    'id' => $id
                    ]
                );
            }
        }
        $this->messageManager->addError(__('We can\'t find a Customer Tier Price to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
