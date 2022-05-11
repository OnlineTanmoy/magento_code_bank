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

namespace Appseconnect\CompanyDivision\Block\Division\Index;

use Magento\Customer\Model\Session;
use Appseconnect\CompanyDivision\Model\DivisionFactory;

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
     * Division grid
     *
     * @var DivisionFactory
     */
    public $divisionFactory;

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
        \Appseconnect\CompanyDivision\Helper\Division\Data $helper,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->helperContactPerson = $helperContactPerson;
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

        if ($this->getDivision()) {
            $pager->setCollection($this->getDivision());
            $this->setChild('pager', $pager);
            //$this->getDivision()->getData();
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
        $this->setTemplate("Appseconnect_CompanyDivision::division/index/listing.phtml");

        return parent::_toHtml();
    }

    /**
     * Get customer
     *
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getDivision()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->customers) {
            $customerData = $this->helperContactPerson->getCustomerId($customerId);
            $this->customers = $this->helper->getChildAllDivision($customerData['customer_id'], true);
        }

        return $this->customers;
    }

    /**
     * Get division url
     *
     * @param int $customerId customer id
     *
     * @return string
     */
    public function getDivisionUrl($customerId)
    {
        return $this->getUrl(
            'division/division/index_select/', [
                'customer_id' => $customerId['division_id']
            ]
        );
    }

    /**
     * Get division view url
     *
     * @param int $customerId customer id
     *
     * @return string
     */
    public function getDivisionViewUrl($customerId)
    {
        return $this->getUrl(
            'division/division/index_add/', [
                'division_id' => $customerId['division_id']
            ]
        );
    }

    /*
     * Get division url
     *
     * @return string
     */
    public function getDivisionAddUrl() {
        return $this->getUrl(
            'division/division/index_add/'
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
