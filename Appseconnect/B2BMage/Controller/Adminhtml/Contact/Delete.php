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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Contact;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

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
     * Customer factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Contact factory
     *
     * @var \Appseconnect\B2BMage\Model\ContactFactory
     */
    public $contactFactory;

    /**
     * Resource model
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * Result page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Order Collection factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * Delete constructor.
     *
     * @param Context                                                    $context                context
     * @param \Magento\Customer\Model\CustomerFactory                    $customerFactory        customer model factory
     * @param \Appseconnect\B2BMage\Model\ContactFactory                 $contactFactory         contact model factory
     * @param \Magento\Framework\App\ResourceConnection                  $resources              resource model
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory order collection factory
     * @param PageFactory                                                $resultPageFactory      result page factory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\ContactFactory $contactFactory,
        \Magento\Framework\App\ResourceConnection $resources,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        PageFactory $resultPageFactory
    ) {
    
        $this->customerFactory = $customerFactory;
        $this->contactFactory = $contactFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resources = $resources;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Action function
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $contactperson_id = $this->_getSession()->getContactpersonId();
        
        $contactPersonId = $this->getRequest()->getParam('id');
        $connection = $this->resources->getConnection();
        $this->_getSession()->unsCustomerId();
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $this->orders = $this->orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $contactPersonId)
            ->setOrder('created_at', 'desc');
        $order = $this->orders->getData();
        try {
            if (! empty($order)) {
                $this->messageManager->addSuccess(__('There is an order under this Contact Person can\'t be deleted.'));
                return $resultRedirect->setPath(
                    'b2bmage/contact/edit/id/' .
                    $contactPersonId .
                    '/customer_id/' .
                    $customerId .
                    'contactperson_id/' .
                    $contactperson_id
                );
            } else {
                if ($customerId) {
                    $ContactDetail = $this->contactFactory->create()->load($contactperson_id);
                    $ContactDetail->delete();
                    
                    $customerDetail = $this->customerFactory->create()->load($ContactDetail['contactperson_id']);
                    $customerDetail->delete();
                    
                    $whereAddress['customer_id=?'] = $customerId;
                    $whereAddress['contact_person_id=?'] = $ContactDetail['contactperson_id'];
                    $salesrepTable = $this->resources->getTableName('insync_contact_address');
                    $connection->delete($salesrepTable, $whereAddress);
                    
                    $whereAddress = [
                        'contactperson_id=?' => $contactperson_id
                    ];
                    $contactpersonTable = $this->resources->getTableName('insync_contactperson');
                    $connection->delete($contactpersonTable, $whereAddress);
                    $this->_getSession()->unsCustomerId();
                    $this->_getSession()->unsContactpersonId();
                    $this->messageManager->addSuccess(__('Contact Person deleted successfully.'));
                    return $resultRedirect->setPath('customer/index/edit/id/' . $customerId);
                }
            }
        } catch (\Exception $e) {
            return $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect->setPath('*/*/');
    }
}
