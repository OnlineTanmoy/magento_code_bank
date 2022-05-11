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
namespace Appseconnect\B2BMage\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total\Collector;
use Appseconnect\B2BMage\Model\QuoteProduct;
use Magento\Quote\Model\Quote\Address\Total\CollectorFactory;
use Magento\Quote\Model\Quote\Address\Total\CollectorInterface;

/**
 * Class TotalsCollector
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class TotalsCollector
{

    /**
     * Total models collector
     *
     * @var \Magento\Quote\Model\Quote\Address\Total\Collector
     */
    public $totalCollector;

    /**
     * CollectorFactory
     *
     * @var \Magento\Quote\Model\Quote\Address\Total\CollectorFactory
     */
    public $totalCollectorFactory;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $eventManager;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * TotalFactory
     *
     * @var \Appseconnect\B2BMage\Model\Quote\TotalFactory
     */
    public $totalFactory;

    /**
     * TotalsCollectorList
     *
     * @var \Magento\Quote\Model\Quote\TotalsCollectorList
     */
    public $collectorList;

    /**
     * Quote validator
     *
     * @var \Magento\Quote\Model\QuoteValidator
     */
    public $quoteValidator;

    /**
     * ShippingFactory
     *
     * @var \Magento\Quote\Model\ShippingFactory
     */
    public $shippingFactory;

    /**
     * ShippingAssignmentFactory
     *
     * @var \Magento\Quote\Model\ShippingAssignmentFactory
     */
    public $shippingAssignmentFactory;

    /**
     * TotalsCollector constructor.
     *
     * @param Collector                                      $totalCollector            TotalCollector
     * @param CollectorFactory                               $totalCollectorFactory     TotalCollectorFactory
     * @param \Magento\Framework\Event\ManagerInterface      $eventManager              EventManager
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data    $helperPriceRule           HelperPriceRule
     * @param \Magento\Store\Model\StoreManagerInterface     $storeManager              StoreManager
     * @param TotalFactory                                   $totalFactory              TotalFactory
     * @param \Magento\Quote\Model\Quote\TotalsCollectorList $collectorList             CollectorList
     * @param \Magento\Quote\Model\ShippingFactory           $shippingFactory           ShippingFactory
     * @param \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory ShippingAssignmentFactory
     * @param \Magento\Quote\Model\QuoteValidator            $quoteValidator            QuoteValidator
     */
    public function __construct(
        Collector $totalCollector,
        CollectorFactory $totalCollectorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Model\Quote\TotalFactory $totalFactory,
        \Magento\Quote\Model\Quote\TotalsCollectorList $collectorList,
        \Magento\Quote\Model\ShippingFactory $shippingFactory,
        \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory,
        \Magento\Quote\Model\QuoteValidator $quoteValidator
    ) {
        $this->totalCollector = $totalCollector;
        $this->totalCollectorFactory = $totalCollectorFactory;
        $this->eventManager = $eventManager;
        $this->helperPriceRule = $helperPriceRule;
        $this->storeManager = $storeManager;
        $this->totalFactory = $totalFactory;
        $this->collectorList = $collectorList;
        $this->shippingFactory = $shippingFactory;
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * Collect
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     *
     * @return \Appseconnect\B2BMage\Model\Quote\Total
     */
    public function collect(\Appseconnect\B2BMage\Model\Quote $quote)
    {
        
        $total = $this->totalFactory->create('\Appseconnect\B2BMage\Model\Quote\Total');
        $this->_collectItemsQtys($quote);
        $total->setSubtotal(0);
        $total->setBaseSubtotal(0);
        $total->setGrandTotal(0);
        $total->setBaseGrandTotal(0);

        foreach ($quote->getAllItems() as $item) {
            if ($this->_initItem($item) && $item->getQty() > 0 && ! $item->isDeleted()) {
                if (! $item->getParentItem()) {
                    $total->setSubtotal((float) $total->getSubtotal() + $item->getRowTotal());
                    $total->setBaseSubtotal((float) $total->getBaseSubtotal() + $item->getBaseRowTotal());
                    
                    $total->setGrandTotal((float) $total->getGrandTotal() + $total->getSubtotal());
                    $total->setBaseGrandTotal((float) $total->getBaseGrandTotal() + $total->getBaseSubtotal());
                }
            }
        }
        $total->setGrandTotal((float) $total->getSubtotal());
        $total->setBaseGrandTotal((float) $total->getBaseSubtotal());
        
        return $total;
    }

    /**
     * InitItem
     *
     * @param $item Item
     *
     * @return bool
     */
    private function _initItem($item)
    {
        if ($item instanceof QuoteProduct && $item->getId()) {
            $quoteItem = $item->getQuote()->getItemById($item->getId());
        } else {
            $quoteItem = $item;
        }
        
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId(
            $quoteItem->getQuote()
                ->getCustomerGroupId()
        );
        $quoteWebsiteId = $quoteItem->getQuote()
            ->getStore()
            ->getWebsiteId();
        
        $originalPrice = $product->getPrice();
        $finalPrice = $product->getFinalPrice($quoteItem->getQty());
        
        $discountedPrice = $this->helperPriceRule->getDiscountedPrice(
            $product->getId(),
            $quoteItem->getQty(),
            $quoteItem->getQuote()
                ->getContactId(),
            $quoteWebsiteId
        );
        
        if ($quoteItem->getParentItem()) {
            $this->_calculateRowTotal($quoteItem, $discountedPrice, $originalPrice);
        } elseif (! $quoteItem->getParentItem()) {
            $childProductId = null;
            foreach ($quoteItem->getChildren() as $child) {
                $childProductId = $child->getProductId();
            }
            if ($childProductId) {
                $discountedPrice = $this->helperPriceRule->getDiscountedPrice(
                    $childProductId,
                    $quoteItem->getQty(),
                    $quoteItem->getQuote()
                        ->getContactId(),
                    $quoteWebsiteId
                );
            }
            $this->_calculateRowTotal($quoteItem, $discountedPrice, $originalPrice);
        }
        return true;
    }

    /**
     * CalculateRowTotal
     *
     * @param $item          Item
     * @param $finalPrice    FinalPrice
     * @param $originalPrice OriginalPrice
     *
     * @return $this
     */
    private function _calculateRowTotal($item, $finalPrice, $originalPrice)
    {
        if (! $originalPrice) {
            $originalPrice = $finalPrice;
        }
        
        $item->setOriginalPrice($originalPrice)->setBaseOriginalPrice($originalPrice);
        if (! $item->getPrice() && ! $item->getBasePrice()) {
            $item->setPrice($finalPrice)->setBasePrice($finalPrice);
        }
        $item->calcRowTotal();
        return $this;
    }

    /**
     * Collect items qty
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     *
     * @return $this
     */
    private function _collectItemsQtys(\Appseconnect\B2BMage\Model\Quote $quote)
    {
        $quote->setItemsCount(0);
        $quote->setItemsQty(0);
        
        foreach ($quote->getAllItems() as $item) {
            $quote->setItemsCount($quote->getItemsCount() + 1);
            $quote->setItemsQty((float) $quote->getItemsQty() + $item->getQty());
        }
        return $this;
    }
}
