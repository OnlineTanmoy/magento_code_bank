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
use Appseconnect\B2BMage\Model\ResourceModel\SalesrepgridFactory;

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
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Customer collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $customers;

    /**
     * Salesrep grid
     *
     * @var SalesrepgridFactory
     */
    public $salesRepResourceFactory;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Listing constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context                 context
     * @param SalesrepgridFactory                              $salesRepResourceFactory salesrep resource
     * @param Session                                          $customerSession         customer session
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory         customer
     * @param array                                            $data                    data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SalesrepgridFactory $salesRepResourceFactory,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->salesRepResourceFactory = $salesRepResourceFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
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
            ->setShowAmounts(true);
        if ($this->getCustomer()) {
            $pager->setCollection($this->getCustomer());
            $this->setChild('pager', $pager);
            $this->getCustomer()->getData();
        }

        return $this;
    }

    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::salesrep/customer/listing.phtml");

        return parent::_toHtml();
    }

    /**
     * Get customer
     *
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getCustomer()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->customers) {
            $this->customers = $this->customerFactory->create()->getCollection();
            $salesRepResourceModel = $this->salesRepResourceFactory->create();

            $this->customers = $salesRepResourceModel->getSalesRepCustomers(
                $this->customers,
                $customerId
            );
            $this->customers->addExpressionAttributeToSelect(
                'name',
                '(CONCAT({{firstname}},"  ",{{lastname}}))',
                [
                    'firstname',
                    'lastname',
                    'customer_status'
                ]
            )->addFieldToFilter('customer_type', 4);
        }

        return $this->customers;
    }

    /**
     * Get contact person url
     *
     * @param int $customerId customer id
     *
     * @return string
     */
    public function getContactPersonUrl($customerId)
    {
        return $this->getUrl(
            'b2bmage/salesrep/customer_view/', [
                'customer_id' => $customerId['entity_id']
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
     * Can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }
}
