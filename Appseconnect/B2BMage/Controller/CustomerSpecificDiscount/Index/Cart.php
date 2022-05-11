<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\CustomerSpecificDiscount\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Class Cart
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Cart extends \Magento\Checkout\Controller\Onepage
{

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;
    
    /**
     * Checkout helper
     *
     * @var \Magento\Checkout\Helper\Data
     */
    public $helperCheckout;
    
    /**
     * Translate inline
     *
     * @var \Magento\Framework\Translate\InlineInterface
     */
    public $translateInline;

    /**
     * Cart constractor
     *
     * @param \Magento\Framework\App\Action\Context              $context             context
     * @param \Magento\Checkout\Helper\Data                      $helperCheckout      checkout helper
     * @param Session                                            $customerSession     customer session
     * @param CustomerRepositoryInterface                        $customerRepository  customer repository
     * @param AccountManagementInterface                         $accountManagement   account management
     * @param \Magento\Framework\Registry                        $coreRegistry        core registry
     * @param \Magento\Framework\Translate\InlineInterface       $translateInline     translate inline
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator    form key
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig         scope config
     * @param \Magento\Framework\View\LayoutFactory              $layoutFactory       layout
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository     quote repository
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory   result page
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory result layout
     * @param \Magento\Framework\Controller\Result\RawFactory    $resultRawFactory    result raw
     * @param \Magento\Framework\Controller\Result\JsonFactory   $resultJsonFactory   result json
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Helper\Data $helperCheckout,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->helperCheckout = $helperCheckout;
        $this->translateInline = $translateInline;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * Checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    private function _getCheckoutSession()
    {
        if (! $this->checkoutSession) {
            $this->checkoutSession = ObjectManager::getInstance()->get(\Magento\Checkout\Model\Session::class);
        }
        return $this->checkoutSession;
    }

    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $grandTotal = $this->getOnepage()
            ->getQuote()
            ->getGrandTotal();
        if ($grandTotal < 0) {
            $this->messageManager->addError(__('Cannot checkout with negative value'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        if (! $this->helperCheckout->canOnepageCheckout()) {
            $this->messageManager->addError(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        
        $quote = $this->getOnepage()->getQuote();
        if (! $quote->hasItems() || $quote->getHasError() || ! $quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        
        if (! $this->_customerSession->isLoggedIn() 
            && ! $this->helperCheckout->isAllowedGuestCheckout($quote)
        ) {
            $this->messageManager->addError(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        
        $this->_customerSession->regenerateId();
        $this->_getCheckoutSession()->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Checkout'));
        return $resultPage;
    }
}
