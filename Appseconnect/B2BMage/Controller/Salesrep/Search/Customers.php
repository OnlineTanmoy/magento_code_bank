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
namespace Appseconnect\B2BMage\Controller\Salesrep\Search;

use Magento\Sales\Controller\OrderInterface;
use Appseconnect\B2BMage\Model\ResourceModel\SalesrepgridFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Customers
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customers extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\]CustomerFactory
     */
    public $customerFactory;

    /**
     * Result json
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;
    
    /**
     * Sales rep resource
     *
     * @var SalesrepgridFactory
     */
    public $salesRepResourceFactory;
    
    /**
     * Customer constractor
     *
     * @param Context                                          $context                 context
     * @param SalesrepgridFactory                              $salesRepResourceFactory salesrep resource
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory       result json
     * @param Session                                          $customerSession         customer session
     * @param PageFactory                                      $resultPageFactory       result page
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory         customer 
     */
    public function __construct(
        Context $context,
        SalesrepgridFactory $salesRepResourceFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
    
        $this->customerFactory = $customerFactory;
        $this->salesRepResourceFactory = $salesRepResourceFactory;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $searchText = $this->getRequest()->getPost('search_text');
            $customerId = $this->customerSession->getCustomerId();
            $salesRepResourceModel = $this->salesRepResourceFactory->create();
            $customerCollection = $this->customerFactory->create()->getCollection();
            
            $customerCollection->addNameToSelect();
            $customerCollection->addFieldToFilter(
                'name', [
                'like' => '%' . $searchText . '%'
                ]
            );
            $customerCollection = $salesRepResourceModel->getSalesRepCustomers(
                $customerCollection,
                $customerId
            );
            $customerCollection->addAttributeToSelect('customer_status')
                ->addFieldToFilter('customer_type', 4);
            
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $resultJson->setData($customerCollection->getData());
        } catch (\Exception $e) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $resultJson->setData([]);
        }
    }
}
