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
namespace Appseconnect\B2BMage\Block\Salesrep\Contact;

use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\SalesrepgridFactory;

/**
* Class Create
*
* @category BLOCK
* @package  Appseconnect
* @author   Insync Magento Team <contact@insync.co.in>
* @license  Insync https://insync.co.in
* @link     https://www.appseconnect.com/
*/
class Create extends \Magento\Framework\View\Element\Template
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
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Salesrep grid
     *
     * @var SalesrepgridFactory
     */
    public $salesRepResourceFactory;
    public $httpContext;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param Session                                          $customerSession customer session
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory customer
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param SalesrepgridFactory                              $salesRepResourceFactory salesrep resource
     * @param array                                            $data            data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        SalesrepgridFactory $salesRepResourceFactory,
        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->customerFactory = $customerFactory;
        $this->salesRepResourceFactory = $salesRepResourceFactory;
        parent::__construct($context, $data);
    }

    /**
    * Get Company list
    *
    * @return false|\Magento\Sales\Model\ResourceModel\Order\Collection
    */
    public function getCompanyList()
    {

        if (!($customerId = $this->httpContext->getValue('customer_id'))) {
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
}
