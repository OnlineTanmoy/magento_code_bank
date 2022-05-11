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
namespace Appseconnect\B2BMage\Block\Salesrep\Customer;

use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\ApproverFactory;

/**
 * Interface View
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Customer
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $customers;
    
    /**
     * Salesrep resource
     *
     * @var SalesrepgridFactory
     */
    public $salesRepResourceFactory;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * View constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context                 context
     * @param ApproverFactory                                  $approverResourceFactory approver resource
     * @param Session                                          $customerSession         customer session
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory         customer
     * @param array                                            $data                    data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ApproverFactory $approverResourceFactory,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
    
        $this->customerSession = $customerSession;
        $this->approverResourceFactory = $approverResourceFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }
    
    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::salesrep/customer/view.phtml");
        
        return parent::_toHtml();
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
            ->setCollection($this->getCustomer());
        $this->setChild('pager', $pager);
        $this->getCustomer()->getData();
        
        return $this;
    }
    
    /**
     * Get customer
     *
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getCustomer()
    {
        if (! ($customerSessionId = $this->customerSession->getCustomerId())) {
            return false;
        }
        
        if (! $this->customers) {
            $approverResourceModel = $this->approverResourceFactory->create();
            $customerId = $this->getRequest()->getParam('customer_id');
            $this->customers = $this->customerFactory->create()->getCollection();
            
            $this->customers = $approverResourceModel->getContacts($customerId, $this->customers);
            $this->customers->addExpressionAttributeToSelect(
                'name',
                '(CONCAT({{firstname}},"  ",{{lastname}}))',
                [
                'firstname',
                'lastname',
                'customer_status'
                ]
            );
        }
        
        return $this->customers;
    }
    
    /**
     * Get contact person login
     *
     * @param int $customrId customer id
     *
     * @return string
     */
    public function getContactPersonLogin($customrId)
    {
        return $this->getUrl(
            'b2bmage/salesrep/customer_login/', [
            'customer_id' => $customrId['entity_id']
            ]
        );
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
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }
        return $this->getUrl('*/*/form');
    }
}
