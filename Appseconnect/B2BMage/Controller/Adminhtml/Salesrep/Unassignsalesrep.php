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
use Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory;

/**
 * Class Unassignsalesrep
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Unassignsalesrep extends \Magento\Backend\App\Action
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
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesRepFactory;
    
    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Unassignsalesrep constructor.
     *
     * @param Context                                     $context           context
     * @param \Magento\Customer\Model\CustomerFactory     $customerFactory   customer
     * @param \Appseconnect\B2BMage\Model\SalesrepFactory $salesRepFactory   salesrep
     * @param Filter                                      $filter            filter
     * @param CollectionFactory                           $collectionFactory salesrep collection
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\SalesrepFactory $salesRepFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->customerFactory = $customerFactory;
        $this->salesRepFactory = $salesRepFactory;
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
        $requestData = $this->getRequest()->getPostValue();
        $salesrepId = $this->getRequest()->getParam('salesrep_id');
        
        foreach ($requestData['customer_id'] as $key => $value) {
            $customerName = $this->_loadCustomer($value)->getName();
            $customer = $this->collectionFactory->create()
                ->addFieldToFilter('salesrep_id', $salesrepId)
                ->addFieldToFilter('customer_id', $value);
            if ($customer->getData()) {
                foreach ($customer->getData() as $data) {
                    $this->_unmap($data);
                    $this->messageManager->addSuccess(
                        __(
                            '%1 has been unassigned',
                            $customerName
                        )
                    );
                }
            } else {
                $this->messageManager->addError(
                    __(
                        '%1 is already unassigned',
                        $customerName
                    )
                );
            }
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(
            'b2bmage/salesrep/edit', [
            'id' => $this->getRequest()
                ->getParam('id')
            ]
        );
    }

    /**
     * Load customer
     *
     * @param int $id customer id
     *
     * @return \Magento\Customer\Model\CustomerFactory
     */
    private function _loadCustomer($id)
    {
        $customer = $this->customerFactory->create()->load($id);
        return $customer;
    }

    /**
     * Unmap
     *
     * @param array $data data
     *
     * @return void
     */
    private function _unmap($data)
    {
        $model = $this->salesRepFactory->create();
        $model->load($data['id']);
        $model->delete();
    }
}
