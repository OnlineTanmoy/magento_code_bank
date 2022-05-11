<?php

namespace Appseconnect\B2BMage\Plugin\Sales\Controller\AbstractController;

use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class Reorder
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

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
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public $storeManager;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    public $redirectFactory;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        Session $session,
        Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->customerSession = $session;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->stockRegistry = $stockRegistry;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->helperContactPerson = $helperContactPerson;
        $this->redirectFactory = $redirectFactory;
    }

    public function afterExecute(
        \Magento\Sales\Controller\AbstractController\Reorder $subject,
        $result
    ) {

        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $currentOrder = $this->_coreRegistry->registry('current_order');

        if ($customerType == 3)
        {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $customerDetail['customer_id'];

            $orderItems = $currentOrder->getAllVisibleItems();

            $this->invalidSku = array();
            $this->outOfStockSku = array();
            foreach ($orderItems as $items)
            {
                $itemSku = $items->getSku();
                $itemQty = $items->getQtyOrdered();

                if ($items->getPrice() != 0) {

                    $this->_cardAddAction($itemSku, $itemQty);
                }

            }

            $quoteId=$this->cart->getQuote()->getId();
            $cartItems = $this->cart->getQuote()->getAllItems();
            $quote = $this->quoteRepository->getActive($quoteId);
            $quoteItems[] = $cartItems;
            $this->quoteRepository->save($quote);
            $quote->collectTotals();

            $this->cart->getCheckoutSession()->setCartWasUpdated(true);
            return $this->redirectFactory->create()
                ->setPath('multiplediscount/cart/form');
        }

        return $result;
    }

    private function _cardAddAction($sku, $qty)
    {
        $_product = $this->productRepository->get($sku);
        $productId = $_product->getId();

        if (isset($_product)) {
            $productStockObj = $this->stockRegistry->getStockItem($productId);
            if ($qty > $productStockObj->getQty() && $productStockObj->getBackorders() == 0) {
                $this->outOfStockSku[] = $sku;
            } else {
                unset($productId);
                $cart = $this->cart->addProduct($_product, $qty);
                $cart->save();
            }
        } else {
            $this->invalidSku[] = $sku;
        }
    }
}