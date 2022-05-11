<?php

namespace Appseconnect\AvailableToPromise\Observer;

use Magento\Framework\Event\ObserverInterface;

class CartSaveAfter implements ObserverInterface
{
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ProductInStockFactory
     */
    public $productInStockFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    public $stockItemRepository;
    /**
     * @var \Appseconnect\AvailableToPromise\Helper\DeliveryDate\Data
     */
    public $helperAvailableToPromise;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    public $dateTimeFactory;

    /**
     * @param \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Appseconnect\AvailableToPromise\Helper\DeliveryDate\Data $helperAvailableToPromise
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Appseconnect\AvailableToPromise\Helper\DeliveryDate\Data $helperAvailableToPromise,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
    ) {
        $this->productInStockFactory = $productInStockFactory;
        $this->productFactory = $productFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->helperAvailableToPromise = $helperAvailableToPromise;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        if ($cart->getItemsCount() > 0) {
            $quoteItemDateArray = array();
            $finalDate = null;
            $quote = $cart->getQuote();
            foreach ($quote->getAllItems() as $quoteItem) {
                if ($quoteItem->getProductType() == 'simple') {
                    $quoteItemQty = $quoteItem->getQty();
                    $productLoad = $this->productFactory->create()->load($quoteItem->getProductId());
                    $productInStockCollection = $this->productInStockFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('product_sku', $productLoad->getSku())
                        ->addFieldToSelect('product_sku')
                        ->addFieldToFilter('available_date',
                            array('gteq' => $this->dateTimeFactory->create()->gmtDate('Y-m-d')))
                        ->setOrder('available_date', 'ASC');

                    $productInStockCollection
                        ->getSelect()
                        ->columns(['available_quantity' => new \Zend_Db_Expr('SUM(available_quantity)')])
                        ->group('product_sku');

                    $finalAtpQty = $productInStockCollection->getFirstItem()->getData('quantity');
                    $stockItem = $productLoad->getExtensionAttributes()->getStockItem();
                    $productQty = $stockItem->getQty();
                    if ($quoteItemQty <= $productQty) {
                        $quoteItem->setDeliveryInfo('');
                    } else {
                        if ($quoteItemQty > $productQty) {
                            if ($productQty > 0) {
                                $additionalQty = $quoteItemQty - $productQty;
                            } else {
                                $additionalQty = $quoteItemQty;
                            }
                            $getNewAvailableDate = $this->helperAvailableToPromise->getAvailableDate($productLoad,
                                $additionalQty);

                            if ($getNewAvailableDate != '') {
                                $quoteItem->setDeliveryInfo(date('Y-m-d', strtotime($getNewAvailableDate)));
                            } else {
                                $quoteItem->setDeliveryInfo('');
                            }
                        }
                    }
                    $quoteItem->save();
                    $quoteItemDateArray[] = $quoteItem->getDeliveryInfo();
                }
            }

            if ($cart->getItemsCount() == 0) {
                $quote->setDeliveryInfo($finalDate);
            } elseif ($cart->getItemsCount() == 1) {
                if (empty($quoteItemDateArray)) {
                    $quote->setDeliveryInfo('');
                } else {
                    $quote->setDeliveryInfo($quoteItemDateArray[0]);
                }
            } else {
                $quote->setDeliveryInfo(max($quoteItemDateArray));
            }
            $quote->save();
        }
    }
}