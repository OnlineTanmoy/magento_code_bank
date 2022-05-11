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
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Salesrep grid
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepgridFactory
     */
    public $salesRepGridFactory;
    
    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Delete constructor.
     *
     * @param Action\Context                                  $context             context
     * @param \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesRepGridFactory salesrep grid
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     customer model
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesRepGridFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
    
        $this->salesRepGridFactory = $salesRepGridFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
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
                $salesrepGridModel = $this->salesRepGridFactory->create();
                $salesrepGridModel->load($id);
                $customer = $this->customerFactory->create()->load($salesrepGridModel->getData('salesrep_customer_id'));
                $customer->setIsDeleteable(true);
                $customer->delete();
                $salesrepGridModel->delete();
                $this->messageManager->addSuccess(__('The Sales Represenatative has been deleted.'));
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
        
        $this->messageManager->addError(__('We can\'t find a Sales Represenatative to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
