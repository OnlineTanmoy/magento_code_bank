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
namespace Appseconnect\B2BMage\Block\CreditLimit\Account\Dashboard;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Model\Session;

/**
 * Interface Listing
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Credit extends \Magento\Framework\View\Element\Template
{
    /**
     * Credit limit
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $creditLimit;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Currency
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Credit constructor.
     *
     * @param \Magento\Backend\Block\Template\Context         $context             context
     * @param \Magento\Directory\Model\CurrencyFactory        $currencyFactory     currency factory
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data   $creditLimit         credit limit
     * @param Session                                         $session             session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param array                                           $data                data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data $creditLimit,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        array $data = []
    ) {
        $this->creditLimit = $creditLimit;
        $this->customerSession = $session;
        $this->currencyFactory = $currencyFactory;
        $this->helperContactPerson = $helperContactPerson;
        parent::__construct($context, $data);
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $price = null;
        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $customerDetail['customer_id'];
            $customerCreditBalance = $this->creditLimit->getCustomerCreditData($customerId);
            if (isset($customerCreditBalance['available_balance'])) {
                $currency = $this->currencyFactory->create();
                $creditBalance = number_format($customerCreditBalance['available_balance'], 2);
                $price = $currency->getCurrencySymbol() . $creditBalance;
            }
        }
        return $price;
    }

}
