<?php

namespace Appseconnect\AvailableToPromise\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ProductInStockFactory
     */
    public $productInStockFactory;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * @param \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->productInStockFactory = $productInStockFactory;
        $this->quoteFactory = $quoteFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $atpDeliveryInfo = $quote->getDeliveryInfo();
        $order->setDeliveryInfo($atpDeliveryInfo);

        foreach ($order->getAllItems() as $orderItem) {
            foreach ($quote->getAllItems() as $quoteItem) {
                if ($quoteItem->getProductType() == 'simple') {
                    $product = $this->productRepository->getById($quoteItem->getProductId());
                    $stockItem = $this->stockRegistry->getStockItemBySku($quoteItem->getSku());
                    $productQty = $product["quantity_and_stock_status"]['qty'];
                    if ($orderItem->getSku() == $quoteItem->getSku() && $quoteItem->getQty() > $productQty) {
                        $orderItem->setDeliveryInfo($quoteItem->getDeliveryInfo());

                        $productInStockCollection = $this->productInStockFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('product_sku', $quoteItem->getSku())
                            ->addFieldToFilter('available_date', array('gteq' => date('Y-m-d')))
                            ->addFieldToFilter('available_quantity', array('gt' => 0))
                            ->setOrder('available_date', 'ASC');

                        $requestedQty = $totalQty = 0;

                        foreach ($productInStockCollection->getData() as $collectionData) {
                            $totalQty += $collectionData['available_quantity'];
                        }

                        $requestedQty = $quoteItem->getQty() - $productQty;
                        if ($requestedQty > 0) {
                            foreach ($productInStockCollection as $collectionData) {
                                if ($totalQty >= $requestedQty) {
                                    $stockItem->setQty(0)->save();
                                    if ($collectionData->getAvailableQuantity() >= $requestedQty) {
                                        $availableBalance = $collectionData->getAvailableQuantity() - $requestedQty;
                                        $collectionData->setAvailableQuantity($availableBalance)->save();
                                        break;
                                    } else {
                                        $requestedQty = $requestedQty - $collectionData->getAvailableQuantity();
                                        $collectionData->setAvailableQuantity(0)->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}