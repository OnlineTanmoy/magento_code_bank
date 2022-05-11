<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\AvailableToPromise\Helper\DeliveryDate;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\Url\Helper\Data
{
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\CollectionFactory
     */
    public $productInStockCollectionFactory;
    /**
     * @var \Appseconnect\AvailableToPromise\Model\ProductInStockFactory
     */
    public $productInStockFactory;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    public $dateTimeFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    public $cart;

    /**
     * @param Context $context
     * @param \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\CollectionFactory $productInStockCollectionFactory
     * @param \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        Context $context,
        \Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock\CollectionFactory $productInStockCollectionFactory,
        \Appseconnect\AvailableToPromise\Model\ProductInStockFactory $productInStockFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->productInStockCollectionFactory = $productInStockCollectionFactory;
        $this->productInStockFactory = $productInStockFactory;
        $this->productRepository = $productRepository;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        parent::__construct($context);
    }


    public function getDeliveryDate($productId)
    {
        $product = $this->productRepository->getById($productId);
        $leadTime = $product->getLeadTime();
        if (!$leadTime) {
            $leadTime = $this->scopeConfig->getValue('insync_availabletopromise/leadtime/time',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        $availabletopromisedata = $this->productInStockCollectionFactory->create()
            ->addFieldToFilter('product_sku', $product->getSku())
            ->addFieldToFilter('available_quantity', array('gt' => 0))
            ->addFieldToFilter('available_date',
                array('gteq' => date('Y-m-d')))
            ->getData();

        $dateArray = [];
        if ($product["quantity_and_stock_status"]['qty'] > 0) {
            return date("Y-m-d", strtotime(sprintf("+%d hours", $leadTime)));
        } else {
            if ($availabletopromisedata) {
                foreach ($availabletopromisedata as $key) {
                    $dateArray[] = date('Y-m-d', strtotime($key['available_date']));
                }
                return min($dateArray);
            }
            return $dateArray;
        }
    }

    public function isDisable()
    {
        if (!$this->cart->getQuote()->getAllItems()) {
            return 1;
        }
        foreach ($this->cart->getQuote()->getAllItems() as $key) {
            if ($key->getProductType() == 'simple') {
                $product = $this->productRepository->getById($key->getProductId());
                $totalQty = $this->getAvailabletoPromiseData($key->getSku()) + $product["quantity_and_stock_status"]['qty'];
                if ($totalQty >= $key->getQty()) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 1;
            }
        }
    }

    public function getAvailabletoPromiseData($productSku)
    {
        $availabletopromisedata = $this->productInStockCollectionFactory->create()
            ->addFieldToFilter('product_sku', $productSku)
            ->addFieldToFilter('available_quantity', array('gt' => 0))
            ->addFieldToFilter('available_date',
                array('gteq' => date('Y-m-d')))
            ->getData();
        $totalQty = 0;
        if ($availabletopromisedata) {
            foreach ($availabletopromisedata as $key) {
                $totalQty += $key['available_quantity'];
            }
        }
        return $totalQty;
    }

    public function getMessage($productSku)
    {
        $product = $this->productRepository->get($productSku);
        $totalQty = $this->getAvailabletoPromiseData($productSku) + $product["quantity_and_stock_status"]['qty'];
        foreach ($this->cart->getQuote()->getAllItems() as $key) {
            if ($key->getSku() == $productSku && $totalQty < $key->getQty()) {
                return ($key->getName() . ' has only ' . $totalQty . ' left');
            }
        }
    }

    public function getAvailableDate($product, $additionalQty)
    {
        $availableDate = '';

        $productInStockCollection = $this->productInStockFactory->create()
            ->getCollection()
            ->addFieldToFilter('product_sku', $product->getSku())
            ->addFieldToFilter('available_date', array('gteq' => $this->dateTimeFactory->create()->gmtDate('Y-m-d')))
            ->setOrder('available_date', 'ASC');

        if ($productInStockCollection->count() > 0) {
            if ($productInStockCollection->getData()) {
                $totalQty = 0;
                foreach ($productInStockCollection->getData() as $collectionData) {
                    $totalQty += $collectionData['available_quantity'];
                    if ($totalQty >= $additionalQty) {
                        $availableDate = $collectionData['available_date'];
                        break;
                    }
                }
            }
        }
        return $availableDate;
    }

    public function getProductType($productId)
    {
        $productType = $this->productRepository->getById($productId)->getTypeId();
        return $productType;
    }

}
