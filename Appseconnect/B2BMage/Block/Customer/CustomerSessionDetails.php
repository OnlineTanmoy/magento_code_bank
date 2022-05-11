<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Customer;

/**
 * Class CustomerSessionDetails
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerSessionDetails extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;

    public $httpContext;

    /**
     * CustomerSessionDetails constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         Context
     * @param \Magento\Framework\App\Http\Context              $httpContext     HttpContext
     * @param \Magento\Customer\Model\Session                  $customerSession CustomerSession
     * @param array                                            $data            Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * GetLogin
     *
     * @return mixed
     */
    public function getLogin()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * GetCustomerData
     *
     * @return mixed
     */
    public function getCustomerData()
    {
        $customerData = $this->customerSession->getCustomer();
        return $customerData;
    }

    public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }

    public function getCustomerType()
    {
        return $this->httpContext->getValue('customer_type');
    }
}
