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
 * Class Assignsalesrep
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Assignsalesrep extends \Magento\Backend\App\Action
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
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory  ;
    
    /**
     * Salesrep model
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesRepFactory  ;

    /**
     * Assignsalesrep constructor.
     *
     * @param Context                                     $context           context
     * @param \Magento\Customer\Model\CustomerFactory     $customerFactory   customer model
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
            $salesRepCollection = $this->collectionFactory->create();
            $salesRepCollection->addFieldToFilter('salesrep_id', $salesrepId);
            $salesRepCollection->addFieldToFilter('customer_id', $value);
            $output = $salesRepCollection->getData();
            if ($output) {
                $this->messageManager->addError(__('%1 is already assigned', $customerName));
            } else {
                $mapData = [
                    'salesrep_id' => $salesrepId,
                    'customer_id' => $value
                ];
                
                $this->_map($mapData);
                $this->messageManager->addSuccess(__('%1 has been assigned', $customerName));
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
     * Map salesrep to customer
     *
     * @param array $data data
     *
     * @return void
     */
    private function _map($data)
    {
        $model = $this->salesRepFactory->create();
        $model->setData($data);
        $model->save();
    }
}
