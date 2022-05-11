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

namespace Appseconnect\B2BMage\Controller\QuickOrder\Cart;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class BulkOrder
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class BulkOrder extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\Page
     */
    public $resultPageFactory;

    /**
     * Csv
     *
     * @var \Magento\Framework\File\Csv
     */
    public $csv;

    /**
     * File uploader
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    public $fileUploaderFactory;

    /**
     * Bulk order validator
     *
     * @var \Appseconnect\B2BMage\Model\BulkOrderValidator
     */
    public $bulkOrderValidator;

    /**
     * Cart
     *
     * @var \Magento\Checkout\Model\Cart
     */
    public $cart;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Invalid Sku
     *
     * @var invalidSku
     */
    public $invalidSku;

    /**
     * Out of stock sku
     *
     * @var outOfStockSku
     */
    public $outOfStockSku;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Bulk order constructor
     *
     * @param Context                                              $context             context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory     $fileUploaderFactory file uploader
     * @param \Appseconnect\B2BMage\Model\BulkOrderValidator       $bulkOrderValidator  bulk order
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory      product
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory     customer
     * @param \Magento\Checkout\Model\Cart                         $cart                cart
     * @param \Magento\Framework\File\Csv                          $csv                 csv
     * @param Session                                              $customerSession     customer session
     * @param PageFactory                                          $resultPageFactory   result page
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry       stock registry
     * @param \Magento\Quote\Api\CartRepositoryInterface           $quoteRepository     quote repository
     */
    public function __construct(
        Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Appseconnect\B2BMage\Model\BulkOrderValidator $bulkOrderValidator,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\File\Csv $csv,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {

        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->csv = $csv;
        $this->bulkOrderValidator = $bulkOrderValidator;
        $this->cart = $cart;
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->stockRegistry = $stockRegistry;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
    }

    /**
     * Redirect to the Quick Order UI page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $uploader = $this->fileUploaderFactory->create(
                [
                    'fileId' => 'sku_file'
                ]
            );
            $fileUploadData = $uploader->validateFile();

            if (isset($fileUploadData["type"])
                && $fileUploadData["type"] != 'application/vnd.ms-excel'
            ) {
                $message = __('Wrong file extension.');
                $this->messageManager->addErrorMessage($message);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('b2bmage/quickorder/cart_productlisting');
                return $resultRedirect;
            }

            $fileContents = $this->csv->getData($fileUploadData["tmp_name"]);

            if (!(in_array('Sku', $fileContents[0]))
                || !(in_array('Qty', $fileContents[0]))
            ) {
                $message = __('Wrong file format.');
                $this->messageManager->addErrorMessage($message);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('b2bmage/quickorder/cart_productlisting');
                return $resultRedirect;
            } else {
                unset($fileContents[0]);
            }

            $this->invalidSku = array();
            $this->outOfStockSku = array();

            foreach ($fileContents as $value) {
                if (isset($value[0]) && $value[0] != '' && $value[0] != null && isset($value[1])) {
                    $sku = $value[0];
                    $qty = $value[1];

                    $this->_cardAddAction($sku, $qty);
                }
            }

            $quoteId=$this->cart->getQuote()->getId();
            $cartItems = $this->cart->getQuote()->getAllItems();
            $quote = $this->quoteRepository->getActive($quoteId);
            $quoteItems[] = $cartItems;
            $this->quoteRepository->save($quote);
            $quote->collectTotals();

            if (!empty($this->invalidSku)) {
                $skuString = implode(', ', $this->invalidSku);
                $this->messageManager->addError(__($skuString . ' skus are not exist.'));
            }

            if (!empty($this->outOfStockSku)) {
                $skuString = implode(', ', $this->outOfStockSku);
                $this->messageManager->addError(__($skuString . ' skus are out od stoc now.'));
            }

            $customerSessionId = $this->customerSession->getCustomerId();
            $customerType = $this->customerFactory->create()
                ->load($this->customerSession->getCustomerId())
                ->getCustomerType();
            if ($customerType == 1) {
                return $this->resultRedirectFactory->create()->setPath('customer/account');
            }
            if (!($customerSessionId)) {
                $this->messageManager->addError(__('Access Denied.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('');
                return $resultRedirect;
            }

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('checkout/cart/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('The file was not uploaded.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('b2bmage/quickorder/cart_productlisting');
            return $resultRedirect;
        }
    }

    /**
     * Cart add
     *
     * @param string $sku sku
     * @param int    $qty qty
     *
     * @return void
     */
    private function _cardAddAction($sku, $qty)
    {
        $productModel = $this->productFactory->create();
        $productId = $productModel->getIdBySku($sku);
        if ($productId) {
            $productStockObj = $this->stockRegistry->getStockItem($productId);
            if ($qty > $productStockObj->getQty() && $productStockObj->getBackorders() == 0) {
                $this->outOfStockSku[] = $sku;
            } else {
                $_product = $productModel->load($productId);
                unset($productId);
                $cart = $this->cart->addProduct($_product, $qty);
                $cart->save();
            }
        } else {
            $this->invalidSku[] = $sku;
        }
    }
}
