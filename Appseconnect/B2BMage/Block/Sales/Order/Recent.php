<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Sales\Order;

use Magento\Customer\Model\Session;

/**
 * Interface Recent
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Recent extends \Magento\Sales\Block\Order\Recent
{
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Recent constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context           $context                context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory order collection
     * @param Session                                                    $customerSession        customer session
     * @param \Magento\Sales\Model\Order\Config                          $orderConfig            order config
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data            $helperContactPerson    contact person helper
     * @param array                                                      $data                   data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        array $data = []
    ) {
    
        $this->helperContactPerson = $helperContactPerson;
        parent::__construct(
            $context,
            $orderCollectionFactory,
            $customerSession,
            $orderConfig,
            $data
        );
    }

    /**
     * Over load constractor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $filterField = "customer_id";
        $customerId = $this->_customerSession->getCustomerId();
        $customerType = $this->_customerSession->getCustomer()->getCustomerType();
        $salesrepId = null;
        $contactId = null;
        if ($customerType == 2) {
            $filterField = "salesrep_id";
        } elseif ($customerType == 3) {
            $contactId = $customerId;
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($contactId);
            $customerId = $parentCustomerMapData['customer_id'];
        }
        
        $orders = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($filterField, $customerId)
            ->addAttributeToFilter(
                'status', [
                'in' => $this->_orderConfig->getVisibleOnFrontStatuses()
                ]
            )
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize('5');
        if ($contactId) {
            $orders->addFieldToFilter('contact_person_id', $contactId);
        }
        $orders->load();
        $this->setOrders($orders);
    }

}
