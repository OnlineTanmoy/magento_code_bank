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

use Magento\Sales\Model\Order\Address;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;
use Magento\Customer\Block\Address\Book as AddressBook;
use Magento\Customer\Model\Customer;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Interface Info
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Info extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;
    
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Payment helper
     *
     * @var \Magento\Payment\Helper\Data
     */
    public $paymentHelper;

    /**
     * Address renderer
     *
     * @var AddressRenderer
     */
    public $addressRenderer;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Helper Quote
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    public $helperQuote;

    /**
     * Customer
     *
     * @var Customer
     */
    public $customerModel;

    /**
     * Is Scope private
     *
     * @var boolean
     */
    public $isScopePrivate;

    /**
     * Info constructor.
     *
     * @param TemplateContext                             $context         context
     * @param Session                                     $customerSession customer session
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuote     helper quote
     * @param AddressBook                                 $addressBook     address book
     * @param Customer                                    $customerModel   customer
     * @param Registry                                    $registry        registry
     * @param PaymentHelper                               $paymentHelper   payment helper
     * @param AddressRenderer                             $addressRenderer address renderer
     * @param array                                       $data            data
     */
    public function __construct(
        TemplateContext $context,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuote,
        AddressBook $addressBook,
        Customer $customerModel,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->session = $customerSession;
        $this->helperQuote = $helperQuote;
        $this->addressBook = $addressBook;
        $this->customerModel = $customerModel;
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $registry;
        $this->isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(
            __(
                'Quote #%1', $this->getQuote()
                    ->getId()
            )
        );
    }
    
    /**
     *  Get catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\Session::class
        );
        return $this->catalogSession;
    }

    /**
     * Get payment info Html
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_current_customer_quote');
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address address
     *
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }

    /**
     * Get default shipping
     *
     * @return NULL|int
     */
    public function getDefaultShipping()
    {
        return $this->customerModel->load(
            $this->getQuote()
                ->getContactId()
        )
            ->getDefaultShipping() ? $this->customerModel->load(
                $this->getQuote()
                    ->getContactId()
            )
            ->getDefaultShipping() : null;
    }

    /**
     * Get address html
     *
     * @param int $defaultShippingId default html
     *
     * @return mixed
     */
    public function getAddressHtml($defaultShippingId)
    {
        return $this->addressBook->getAddressHtml(
            $this->addressBook->getAddressById($defaultShippingId)
        );
    }

    /**
     * Get checkout post json
     *
     * @return string
     */
    public function getCheckoutPostJson()
    {
        return $this->helperQuote->getCheckoutPostJson($this->getQuote());
    }

    /**
     * Get salesrep id
     *
     * @return NULL|int
     */
    public function getSalesrepId()
    {
        return $this->getCatalogSession()->getSalesrepId() ?
        $this->getCatalogSession()->getSalesrepId() : null;
    }
}
