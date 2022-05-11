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
namespace Appseconnect\B2BMage\Block\Sales\Approve;

use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\OrderApproverFactory;

/**
 * Interface Listing
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Listing extends \Magento\Framework\View\Element\Template
{

    /**
     * Order collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Order
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $orders;

    /**
     * Order config
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    public $orderConfig;

    /**
     * Customer
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $customers;

    /**
     * Order Approver
     *
     * @var OrderApproverFactory
     */
    public $orderApproverResourceFactory;

    /**
     * Listing constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context           $context                      context
     * @param OrderApproverFactory                                       $orderApproverResourceFactory order approver resource
     * @param Session                                                    $customerSession              customer session
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory       order collection
     * @param \Magento\Sales\Model\Order\Config                          $orderConfig                  order config
     * @param array                                                      $data                         data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        OrderApproverFactory $orderApproverResourceFactory,
        Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderApproverResourceFactory = $orderApproverResourceFactory;
        $this->orderConfig = $orderConfig;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::sales/approve/listing.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Get pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );
        $pager->setLimit(10)
            ->setShowAmounts(true)
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        
        return $this;
    }

    /**
     * Can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * Get orders
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (! ($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (! $this->orders) {
            $orderApproverResource = $this->orderApproverResourceFactory->create();
            $this->orders = $this->orderCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter(
                    'main_table.status', [
                    'in' => $this->orderConfig->getVisibleOnFrontStatuses()
                    ]
                )
                ->setOrder('main_table.created_at', 'desc');
            
            $approvalOrders = $orderApproverResource->getApprovalOrders(
                $customerId,
                $this->orders
            );
            $approvalOrders->addFieldToFilter('main_table.status', 'holded');
            $this->orders = $approvalOrders;
        }
        return $this->orders;
    }

    /**
     * Get order edit url
     *
     * @param id $id id
     *
     * @return static
     */
    public function getOrderEditUrl($id)
    {
        return $this->getUrl(
            'b2bmage/sales/order_edit/', [
            'order_id' => $id,
            'approver' => 'yes'
            ]
        );
    }
    
    /**
     * Get view url
     *
     * @param id $id id
     *
     * @return static
     */
    public function getViewUrl($id)
    {
        return $this->getUrl(
            'sales/order/view/', [
            'order_id' => $id,
            'approver' => 'yes'
            ]
        );
    }
    
    /**
     * Get action url
     *
     * @return static
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/approve_order/', []);
    }
}
