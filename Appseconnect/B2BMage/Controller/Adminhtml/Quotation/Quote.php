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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Quotation;

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\Quote\Email\Sender\QuoteCommentSender;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

/**
 * Class Quote
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class Quote extends \Magento\Backend\App\Action
{

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var string[]
     */
    public $publicActions = [
        'view',
        'index'
    ];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * File
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    /**
     * Translate online
     *
     * @var \Magento\Framework\Translate\InlineInterface
     */
    public $translateInline;

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Result json
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Result layout
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Result raw
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory;

    /**
     * Order Management
     *
     * @var OrderManagementInterface
     */
    public $orderManagement;

    /**
     * Order Repository
     *
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * Logger interface
     *
     * @var LoggerInterface
     */
    public $logger;

    /**
     * File factory
     *
     * @var \Zend_Filter_LocalizedToNormalizedFactory
     */
    public $filterFactory;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $url;

    /**
     * Quote comment sender
     *
     * @var QuoteCommentSender
     */
    public $quoteCommentSender;

    /**
     * Resolver
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $resolver;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    public $escaper;

    /**
     * Quotation helper
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    public $helperQuotation;

    /**
     * Custom cart
     *
     * @var \Appseconnect\B2BMage\Model\CustomCart
     */
    public $customCart;

    /**
     * Quotation Repository
     *
     * @var \Appseconnect\B2BMage\Model\QuotationRepository
     */
    public $quotationRepository;

    /**
     * Custom quote
     *
     * @var \Appseconnect\B2BMage\Model\Quote
     */
    public $customerQuote;

    /**
     * Quote constructor.
     *
     * @param Action\Context                                   $context             context
     * @param \Zend_Filter_LocalizedToNormalizedFactory        $filterFactory       filter
     * @param \Magento\Framework\Locale\ResolverInterface      $resolver            resolver
     * @param \Magento\Framework\Escaper                       $escaper             escaper
     * @param \Magento\Framework\UrlInterface                  $url                 url
     * @param QuoteCommentSender                               $quoteCommentSender  quote comment sender
     * @param \Appseconnect\B2BMage\Model\CustomCart           $customCart          custom cart
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data      $helperQuotation     quotation helper
     * @param \Appseconnect\B2BMage\Model\QuotationRepository  $quotationRepository quotation repository
     * @param \Appseconnect\B2BMage\Model\Quote                $customerQuote       customer quote
     * @param \Magento\Framework\Registry                      $coreRegistry        core register
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory         file
     * @param \Magento\Framework\Translate\InlineInterface     $translateInline     translate inline
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory   result page
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory   result json
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory result layout
     * @param \Magento\Framework\Controller\Result\RawFactory  $resultRawFactory    result raw
     * @param OrderManagementInterface                         $orderManagement     order management
     * @param OrderRepositoryInterface                         $orderRepository     order repository
     * @param LoggerInterface                                  $logger              logger
     */
    public function __construct(
        Action\Context $context,
        \Zend_Filter_LocalizedToNormalizedFactory $filterFactory,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\UrlInterface $url,
        QuoteCommentSender $quoteCommentSender,
        \Appseconnect\B2BMage\Model\CustomCart $customCart,
        \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuotation,
        \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository,
        \Appseconnect\B2BMage\Model\Quote $customerQuote,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
    
        $this->coreRegistry = $coreRegistry;
        $this->filterFactory = $filterFactory;
        $this->url = $url;
        $this->quoteCommentSender = $quoteCommentSender;
        $this->resolver = $resolver;
        $this->escaper = $escaper;
        $this->helperQuotation = $helperQuotation;
        $this->customCart = $customCart;
        $this->quotationRepository = $quotationRepository;
        $this->customerQuote = $customerQuote;
        $this->fileFactory = $fileFactory;
        $this->translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->orderManagement = $orderManagement;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_B2BMage::customer_quote');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));
        return $resultPage;
    }

    /**
     * Goback
     *
     * @param string $backUrl backurl
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($backUrl || $backUrl = $this->_redirect->getRefererUrl()) {
            $resultRedirect->setUrl($backUrl);
        }
        
        return $resultRedirect;
    }

    /**
     * Initialize quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote|false
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
     * Is valid post request
     *
     * @return bool
     */
    public function isValidPostRequest()
    {
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        return ($formKeyIsValid && $isPost);
    }
}
