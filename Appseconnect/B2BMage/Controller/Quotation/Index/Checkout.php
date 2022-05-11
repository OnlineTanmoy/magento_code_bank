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
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Checkout
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Checkout extends \Magento\Framework\App\Action\Action
{
    /**
     * Quote
     *
     * @var QuoteInterface
     */
    public $quote;

    /**
     * Filter
     *
     * @var \Zend_Filter_LocalizedToNormalizedFactory
     */
    public $filterFactory;

    /**
     * Resolver manager
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $resolverManager;

    /**
     * Cart
     *
     * @var \Magento\Checkout\Model\Cart
     */
    public $cart;

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
     * Product
     *
     * @var \Magento\Catalog\Model\Product
     */
    public $productModel;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\Customer
     */
    public $customerModel;

    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Quote product
     *
     * @var QuoteProductInterface
     */
    public $quoteProduct;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Page
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Checkout constructor.
     *
     * @param Context                                         $context             context
     * @param \Zend_Filter_LocalizedToNormalizedFactory       $filterFactory       filter
     * @param \Magento\Framework\Locale\ResolverInterface     $resolverManager     resolver manager
     * @param \Magento\Checkout\Model\Cart                    $cart                cart
     * @param \Appseconnect\B2BMage\Model\CustomCart          $customCart          custom cart
     * @param \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository quotation repository
     * @param \Magento\Catalog\Model\Product                  $productModel        product model
     * @param \Magento\Customer\Model\Customer                $customerModel       customer model
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param QuoteInterface                                  $quote               quote
     * @param QuoteProductInterface                           $quoteProduct        quote product
     * @param Session                                         $customerSession     customer session
     * @param \Magento\Catalog\Model\ProductFactory           $productFactory      product
     * @param PageFactory                                     $resultPageFactory   result page
     */
    public function __construct(
        Context $context,
        \Zend_Filter_LocalizedToNormalizedFactory $filterFactory,
        \Magento\Framework\Locale\ResolverInterface $resolverManager,
        \Magento\Checkout\Model\Cart $cart,
        \Appseconnect\B2BMage\Model\CustomCart $customCart,
        \Appseconnect\B2BMage\Model\QuotationRepository $quotationRepository,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Customer\Model\Customer $customerModel,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        QuoteInterface $quote,
        QuoteProductInterface $quoteProduct,
        Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        PageFactory $resultPageFactory
    ) {
        $this->quote = $quote;
        $this->filterFactory = $filterFactory;
        $this->resolverManager = $resolverManager;
        $this->cart = $cart;
        $this->customCart = $customCart;
        $this->quotationRepository = $quotationRepository;
        $this->productModel = $productModel;
        $this->customerModel = $customerModel;
        $this->helperContactPerson = $helperContactPerson;
        $this->quoteProduct = $quoteProduct;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * Init product
     *
     * @param int|NULL $productId product id
     *
     * @return ProductRepositoryInterface|boolean
     */
    private function _initProduct($productId = null)
    {
        $productId = $productId ? $productId : (int)$this->getRequest()->getParam('product_id');
        if ($productId) {
            try {
                return $this->productFactory->create()->load($productId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Checkout execute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        try {
            foreach ($this->cart->getQuote()->getAllItems() as $key) {
                $this->cart->removeItem($key->getId());
            }
            $quote = $this->quotationRepository->get($quoteId);
            $items = $quote->getItemsCollection();
            $count = 0;
            $quoteDetail = array("id" => $quote->getId());
            $this->customerSession->setQuotationData($quoteDetail);
            $productData = array();
            $parentData = array();
            foreach ($items as $item) {
                $productId = $item->getProductId();
                $price = $item->getPrice();
                $productData[$productId] = $price;
                if ($item->getParentItemId()) {
                    $productData[$productId] = $parentData[$item->getParentItemId()];
                }
                if ($item->getProductType() == "configurable") {
                    $parentData[$item->getId()] = $price;
                }
            }
            foreach ($items as $item) {
                $itemId = $item->getId();
                if (isset($parentData[$itemId])) {
                    $productId = $item->getProductId();
                    $productData[$productId] = $parentData[$itemId];
                }
                $this->customerSession->setQuotationProduct($productData);
                if ($item->getParentItemId()) {
                    continue;
                }
                $params['qty'] = $item->getQty();
                if (isset($params['qty'])) {
                    $filter = $this->filterFactory->create(
                        [
                        'locale' => $this->resolverManager->getLocale()
                        ]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
                if ($superAttribute = $item->getSuperAttribute()) {
                    $superAttributeData = json_decode($superAttribute, true);
                    $params['super_attribute'] = $superAttributeData;
                }
                $params["product"] = $item->getProductId();
                $product = $this->_initProduct($item->getProductId());
                $this->cart->addProduct($product, $params);
                $count++;
            }
            $this->customerSession->unsQuotationProduct();
            $this->cart->save();
            $message = __('You added %1 item(s) to your cart.', $count);
            $this->messageManager->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkout/cart/');
        return $resultRedirect;
    }
}
