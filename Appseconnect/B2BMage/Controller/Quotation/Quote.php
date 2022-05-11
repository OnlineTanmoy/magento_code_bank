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
namespace Appseconnect\B2BMage\Controller\Quotation;

use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Framework\App\ObjectManager;
use Appseconnect\B2BMage\Model\Quote\Email\Sender\QuoteCommentSender;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Registry;
use Appseconnect\B2BMage\Model\CustomCart as CustomCart;

/**
 * Class Quote
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class Quote extends \Magento\Framework\App\Action\Action implements ViewInterface
{

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Form key
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    public $formKeyValidator;

    /**
     * Cart
     *
     * @var \Magento\Checkout\Model\Cart
     */
    public $cart;
   
    /**
     * Filter
     *
     * @var \Zend_Filter_LocalizedToNormalizedFactory
     */
    public $filterFactory;
   
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
   
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    public $escaper;
   
    /**
     * Url manager
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $urlManager;
   
    /**
     * Comment sender
     *
     * @var QuoteCommentSender
     */
    public $commentSender;
   
    /**
     * Resolver
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $resolver;
   
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;
   
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
   
    /**
     * Quotation repository
     *
     * @var \Appseconnect\B2BMage\Model\QuotationRepository
     */
    public $quotationRepository;
   
    /**
     * Checkout session
     *
     * @var Session
     */
    public $checkoutSession;
   
    /**
     * Custom cart
     *
     * @var CustomCart
     */
    public $customCart;
   
    /**
     * Quote constructor
     *
     * @param \Magento\Framework\App\Action\Context              $context             context
     * @param \Zend_Filter_LocalizedToNormalizedFactory          $filterFactory       filter
     * @param \Psr\Log\LoggerInterface                           $logger              logger
     * @param \Magento\Framework\Escaper                         $escaper             escaper
     * @param \Magento\Framework\Locale\ResolverInterface        $resolver            resolver
     * @param \Magento\Framework\UrlInterface                    $urlManager          Url manager
     * @param QuoteCommentSender                                 $commentSender       comment sender
     * @param \Magento\Customer\Model\CustomerFactory            $customerFactory     customer
     * @param Registry                                           $coreRegistry        core registry
     * @param \Appseconnect\B2BMage\Model\QuotationRepository    $quotationRepository quotation repository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig         scope config
     * @param Session                                            $checkoutSession     checkout session
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager        store manager
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator    form key
     * @param CustomCart                                         $customCart          custom cart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Zend_Filter_LocalizedToNormalizedFactory $filterFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\UrlInterface $urlManager,
        QuoteCommentSender $commentSender,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Registry $coreRegistry,
        \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomCart $customCart
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->filterFactory = $filterFactory;
        $this->logger = $logger;
        $this->escaper = $escaper;
        $this->urlManager = $urlManager;
        $this->commentSender = $commentSender;
        $this->resolver = $resolver;
        $this->coreRegistry = $coreRegistry;
        $this->customerFactory = $customerFactory;
        $this->quotationRepository = $quotationRepository;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->customCart = $customCart;
        parent::__construct($context);
    }

    /**
     * Set back redirect url to response
     *
     * @param null|string $backUrl back url
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($backUrl || $backUrl = $this->getBackUrl($this->_redirect->getRefererUrl())) {
            $resultRedirect->setUrl($backUrl);
        }
        
        return $resultRedirect;
    }

    /**
     * Init quote
     *
     * @return boolean|\Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     */
    public function initQuote()
    {
        $id = $this->getRequest()->getParam('quote_id');
        try {
            $quote = $this->quotationRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->coreRegistry->register('insync_customer_quote', $quote);
        $this->coreRegistry->register('insync_current_customer_quote', $quote);
        return $quote;
    }

    /**
     * Check if URL corresponds store
     *
     * @param string $url url
     *
     * @return bool
     */
    public function isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        $store = $this->storeManager->getStore();
        $unsecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos(
            $url,
            $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)
        ) === 0;
        return $unsecure || $secure;
    }

    /**
     * Get resolved back url
     *
     * @param null $defaultUrl default url
     *
     * @return mixed|null|string
     */
    public function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();
            return $returnUrl;
        }
        
        $shouldRedirectToCart = $this->scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        if ($shouldRedirectToCart || $this->getRequest()->getParam('in_cart')) {
            if ($this->getRequest()->getActionName() == 'add'
                && ! $this->getRequest()->getParam('in_cart')
            ) {
                $this->checkoutSession->setContinueShoppingUrl($this->_redirect->getRefererUrl());
            }
            return $this->_url->getUrl('checkout/cart');
        }
        
        return $defaultUrl;
    }
    
    /**
     * Catalog session
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
}
