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

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\CustomerFactory;

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
     * Special price customer
     *
     * @var CustomerFactory
     */
    public $specialPriceCustomerFactory;
    
    /**
     * Delete constructor
     *
     * @param Action\Context  $context                     context
     * @param CustomerFactory $specialPriceCustomerFactory special price customer
     */
    public function __construct(
        Action\Context $context,
        CustomerFactory $specialPriceCustomerFactory
    ) {
        parent::__construct($context);
        $this->specialPriceCustomerFactory = $specialPriceCustomerFactory;
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
                $specialPriceModel = $this->specialPriceCustomerFactory->create();
                $specialPriceModel->load($id);
                $specialPriceModel->delete();
                $this->messageManager->addSuccess(__('The Customer Special Price has been deleted.'));
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
        $this->messageManager->addError(__('We can\'t find a Customer Special Price to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
