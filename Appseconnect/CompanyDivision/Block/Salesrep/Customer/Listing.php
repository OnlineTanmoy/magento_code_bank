<?php
namespace Appseconnect\CompanyDivision\Block\Salesrep\Customer;

use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\SalesrepgridFactory;

class Listing extends \Appseconnect\B2BMage\Block\Salesrep\Customer\Listing
{
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
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        array $data = []
    ) {
        $this->divisionHelper = $divisionHelper;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $salesRepResourceFactory, $customerSession, $customerFactory, $data);
    }

    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_CompanyDivision::salesrep/customer/listing.phtml");

        return $this->fetchView($this->getTemplateFile());;
    }

    public function getParentDivision($customerId)
    {
        $division = $this->divisionHelper->divisionByCustomerId($customerId);
        $customer = $this->customerFactory->create()->load($division->getCustomerId());
        return $customer->getName();
    }
}
