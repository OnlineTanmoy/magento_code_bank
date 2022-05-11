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
class Add extends \Magento\Framework\View\Element\Template
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

    public function getCompanyList() {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->customers) {
            $customerData = $this->helperContactPerson->getCustomerId($customerId);
            $this->customers = $this->helper->getChildAllDivision($customerData['customer_id'], true);
        }

        return $this->customers;
    }
}
