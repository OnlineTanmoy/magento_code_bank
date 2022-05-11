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

use Appseconnect\B2BMage\Model\PriceFactory;
use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Delete extends Action
{
    
    /**
     * Pricelist price
     *
     * @var PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context               context
     * @param PriceFactory   $pricelistPriceFactory pricelist price
     */
    public function __construct(
        Action\Context $context,
        PriceFactory $pricelistPriceFactory
    ) {
    
        $this->pricelistPriceFactory = $pricelistPriceFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->pricelistPriceFactory->create();
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                $this->messageManager->addSuccess(__('The pricelist has been deleted.'));
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
        $this->messageManager->addError(__('We can\'t find a pricelist to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
