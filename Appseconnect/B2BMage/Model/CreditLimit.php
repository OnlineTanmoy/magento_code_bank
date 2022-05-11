<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Appseconnect\B2BMage\Helper\ContactPerson\Data as ContactPersonHelper;
use Appseconnect\B2BMage\Helper\CreditLimit\Data as CreditLimitHelper;

/**
 * Class CreditLimit
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreditLimit extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;

    /**
     * Credit limit helper
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $creditLimitHelper;

    /**
     * Payment code
     *
     * @var string
     */
    const PAYMENT_METHOD_CREDIT_LIMIT_CODE = 'creditlimit';

    /**
     * Payment method code
     *
     * @var string
     */
    public $_code = self::PAYMENT_METHOD_CREDIT_LIMIT_CODE;

    /**
     * Quote
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * CreditLimit constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context                context
     * @param Session                                                      $customerSession        customer session
     * @param ContactPersonHelper                                          $contactPersonHelper    contact person helper
     * @param CreditLimitHelper                                            $creditLimitHelper      credit limit helper
     * @param \Magento\Framework\Registry                                  $registry               registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory            $extensionFactory       extension factory
     * @param \Magento\Framework\Api\AttributeValueFactory                 $customAttributeFactory customer attribute
     * @param \Magento\Payment\Helper\Data                                 $paymentData            payment data
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig            scopeconfig
     * @param \Magento\Payment\Model\Method\Logger                         $logger                 logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource               resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection     resource collection
     * @param array                                                        $data                   data
     * @param \Magento\Quote\Model\QuoteFactory                            $quoteFactory           quote
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        Session $customerSession,
        ContactPersonHelper $contactPersonHelper,
        CreditLimitHelper $creditLimitHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->creditLimitHelper = $creditLimitHelper;
        $this->quoteFactory = $quoteFactory;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Get checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    private function _getCheckoutSession()
    {
        $this->checkoutSession = ObjectManager::getInstance()->create(\Magento\Checkout\Model\Session::class);
        return $this->checkoutSession;
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote quote
     *
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
            return false;
        }

        if ($this->customerSession->getCustomerId()) {
            $customerId = $this->customerSession->getCustomerId();
        } else {
            $customerId = $quote->getCustomerId();
        }
        $customerType = null;
        if ($customerId) {
            $customerDetail = $this->contactPersonHelper->getCustomerData($customerId);
            $customerType = $customerDetail['customer_type'];
        }

        $quoteId = $quote->getId();
        $quote = $this->_getCheckoutSession()->getQuote();
        if (!$quote->getGrandTotal()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
        }

        if ($quote && $customerId && $customerType == 3) {
            $b2bcustomerDetail = $this->contactPersonHelper->getCustomerId($customerId);
            $customerCreditBalance = $this->creditLimitHelper->getCustomerCreditData($b2bcustomerDetail['customer_id']);
            $quoteData = $quote->getData();
            $grandTotal = $quoteData['grand_total'];
            if (isset($customerCreditBalance['available_balance']) && $customerCreditBalance['available_balance'] >= $grandTotal && $grandTotal > 0) {
                return true;
            }
        }

        return false;
    }
}
