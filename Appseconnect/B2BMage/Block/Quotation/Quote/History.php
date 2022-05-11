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
namespace Appseconnect\B2BMage\Block\Quotation\Quote;

use Magento\Framework\App\ObjectManager;
use Appseconnect\B2BMage\Model\ResourceModel\Quote\CollectionFactoryInterface;
use Magento\Customer\Model\Session;

/**
 * Interface History
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class History extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Quote
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Quote\Collection
     */
    public $quotes;

    /**
     * Quote collection
     *
     * @var CollectionFactoryInterface
     */
    public $quoteCollectionFactory;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Helpwr contact person
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context             context
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory     cutomer
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data  $helperContactPerson contact p[erson helper
     * @param Session                                          $customerSession     customer session
     * @param array                                            $data                data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Overloade constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotes'));
    }

    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::quotation/quote/history.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Get quote collection
     *
     * @return CollectionFactoryInterface
     *
     * @deprecated 100.1.1
     */
    public function getQuoteCollectionFactory()
    {
        if ($this->quoteCollectionFactory === null) {
            $this->quoteCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->quoteCollectionFactory;
    }

    public function getCustomerId()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $customerId;
    }

    /**
     * Get quote
     *
     * @return bool|\Appseconnect\B2BMage\Model\ResourceModel\Quote\Collection
     */
    public function getQuotes()
    {
        $customerId = $this->customerSession->getCustomerId();

        $contactPersonRole = $this->helperContactPerson->isAdministrator($customerId);

        $parentCustomerId = null;
        if ($customerId && $this->customerSession->getCustomer()->getCustomerType() == 3) {
            $parentCustomer = $this->helperContactPerson->getCustomerId($customerId);
            $parentCustomerId = $parentCustomer['customer_id'];
        }

        if (!$customerId || !$parentCustomerId) {
            return false;
        }
        
        if (!$this->quotes) {

            if ($contactPersonRole == 1) {
                $this->quotes = $this->getQuoteCollectionFactory()
                    ->create()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('status', array('neq' => 'closed'))
                    ->addFieldToFilter('customer_id', $parentCustomerId)
                    ->setOrder('created_at', 'desc');
            } else {
                $this->quotes = $this->getQuoteCollectionFactory()
                    ->create($parentCustomerId, $customerId)
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('status', array('neq' => 'closed'))
                    ->setOrder('created_at', 'desc');
            }
        }
        return $this->quotes;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuotes()) {
            $pager = $this->getLayout()
                ->createBlock(\Magento\Theme\Block\Html\Pager::class, 'sales.order.history.pager')
                ->setCollection($this->getQuotes());
            $this->setChild('pager', $pager);
            $this->getQuotes()->load();
        }
        return $this;
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
     * Get edit url
     *
     * @param object $quote quote
     *
     * @return string
     */
    public function getEditUrl($quote)
    {
        return $this->getUrl(
            'b2bmage/quotation/index_edit', [
            'quote_id' => $quote->getId()
            ]
        );
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
    
    /**
     * Get customer name
     *
     * @param int $id customer id
     *
     * @return string
     */
    public function getCustomerName($id)
    {
        $name = null;
        $model = $this->customerFactory->create();
        $customer = $model->load($id);
        $name = $customer->getName();
        return $name;
    }
}
