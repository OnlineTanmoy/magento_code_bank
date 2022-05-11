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
namespace Appseconnect\B2BMage\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Appseconnect\B2BMage\Model\Quote\Product\AbstractItem;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface;

/**
 * Class QuoteProduct
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteProduct extends AbstractItem implements QuoteProductInterface
{

    /**
     * Quote model object
     *
     * @var \Appseconnect\B2BMage\Model\Quote
     */
    public $quote;

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct');
    }

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(QuoteProductInterface::ID);
    }

    /**
     * Set Id
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(QuoteProductInterface::ID, $id);
    }

    /**
     * Get Quote Id
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->getData(QuoteProductInterface::QUOTE_ID);
    }

    /**
     * Set Quote Id
     *
     * @param int $quoteId quote id
     *
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(QuoteProductInterface::QUOTE_ID, $quoteId);
    }

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(QuoteProductInterface::CUSTOMER_ID);
    }

    /**
     * Set Customer Id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(QuoteProductInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->getData(QuoteProductInterface::PRODUCT_ID);
    }

    /**
     * Set Product Id
     *
     * @param int $productId product id
     *
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(QuoteProductInterface::PRODUCT_ID, $productId);
    }

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getProductSku()
    {
        return $this->getData(QuoteProductInterface::PRODUCT_SKU);
    }

    /**
     * Set Product Sku
     *
     * @param string $productSku product sku
     *
     * @return $this
     */
    public function setProductSku($productSku)
    {
        return $this->setData(QuoteProductInterface::PRODUCT_SKU, $productSku);
    }

    /**
     * Get Qty
     *
     * @return int|null
     */
    public function getQty()
    {
        return $this->getData(QuoteProductInterface::QTY);
    }
    
    /**
     * Get Row Total
     *
     * @return float|null
     */
    public function getRowTotal()
    {
        return $this->getData(QuoteProductInterface::ROW_TOTAL);
    }

    /**
     * Set Row Total
     *
     * @param float $rowTotal rowtotal
     *
     * @return $this
     */
    public function setRowTotal($rowTotal)
    {
        return $this->setData(QuoteProductInterface::ROW_TOTAL, $rowTotal);
    }

    /**
     * Get Base Row Total
     *
     * @return float|null
     */
    public function getBaseRowTotal()
    {
        return $this->getData(QuoteProductInterface::BASE_ROW_TOTAL);
    }

    /**
     * Set Base Row Total
     *
     * @param float $baseRowTotal baserowtotal
     *
     * @return $this
     */
    public function setBaseRowTotal($baseRowTotal)
    {
        return $this->setData(QuoteProductInterface::BASE_ROW_TOTAL, $baseRowTotal);
    }

    /**
     * Get Price
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->getData(QuoteProductInterface::PRICE);
    }

    /**
     * Set Price
     *
     * @param float $price price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(QuoteProductInterface::PRICE, $price);
    }

    /**
     * Get Base Price
     *
     * @return float|null
     */
    public function getBasePrice()
    {
        return $this->getData(QuoteProductInterface::BASE_PRICE);
    }

    /**
     * Set Base Price
     *
     * @param float $basePrice base price
     *
     * @return $this
     */
    public function setBasePrice($basePrice)
    {
        return $this->setData(QuoteProductInterface::BASE_PRICE, $basePrice);
    }

    /**
     * Get Original Price
     *
     * @return float|null
     */
    public function getOriginalPrice()
    {
        return $this->getData(QuoteProductInterface::ORIGINAL_PRICE);
    }

    /**
     * Set Original Price
     *
     * @param float $originalPrice original price
     *
     * @return $this
     */
    public function setOriginalPrice($originalPrice)
    {
        return $this->setData(QuoteProductInterface::ORIGINAL_PRICE, $originalPrice);
    }

    /**
     * Get Base Original Price
     *
     * @return float|null
     */
    public function getBaseOriginalPrice()
    {
        return $this->getData(QuoteProductInterface::BASE_ORIGINAL_PRICE);
    }

    /**
     * Set Base Original Price
     *
     * @param float $baseOriginalPrice base original price
     *
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice)
    {
        return $this->setData(QuoteProductInterface::BASE_ORIGINAL_PRICE, $baseOriginalPrice);
    }

    /**
     * Get Parent Item Id
     *
     * @return int|null
     */
    public function getParentItemId()
    {
        return $this->getData(QuoteProductInterface::PARENT_ITEM_ID);
    }

    /**
     * Set Parent Item Id
     *
     * @param int $parentItemId parent item id
     *
     * @return $this
     */
    public function setParentItemId($parentItemId)
    {
        return $this->setData(QuoteProductInterface::PARENT_ITEM_ID, $parentItemId);
    }

    /**
     * Get Weight
     *
     * @return string|null
     */
    public function getWeight()
    {
        return $this->getData(QuoteProductInterface::WEIGHT);
    }

    /**
     * Set Weight
     *
     * @param string $weight weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        return $this->setData(QuoteProductInterface::WEIGHT, $weight);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(QuoteProductInterface::NAME);
    }

    /**
     * Set Name
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(QuoteProductInterface::NAME, $name);
    }

    /**
     * Get Product Type
     *
     * @return string|null
     */
    public function getProductType()
    {
        $product = $this->getProduct();
        if ($product) {
            return $product->getTypeId();
        }
        return $this->getData(QuoteProductInterface::PRODUCT_TYPE);
    }

    /**
     * Set Product Type
     *
     * @param string $productType product type
     *
     * @return $this
     */
    public function setProductType($productType)
    {
        return $this->setData(QuoteProductInterface::PRODUCT_TYPE, $productType);
    }

    /**
     * Get Super Attribute
     *
     * @return string|null
     */
    public function getSuperAttribute()
    {
        return $this->getData(QuoteProductInterface::SUPER_ATTRIBUTE);
    }

    /**
     * Set Super Attribute
     *
     * @param string $superAttribute super attribute
     *
     * @return $this
     */
    public function setSuperAttribute($superAttribute)
    {
        return $this->setData(QuoteProductInterface::SUPER_ATTRIBUTE, $superAttribute);
    }

    /**
     * Get Is Virtual
     *
     * @return int|null
     */
    public function getIsVirtual()
    {
        return $this->getData(QuoteProductInterface::IS_VIRTUAL);
    }

    /**
     * Set Is Virtual
     *
     * @param int $isVirtual is virtual
     *
     * @return $this
     */
    public function setIsVirtual($isVirtual)
    {
        return $this->setData(QuoteProductInterface::IS_VIRTUAL, $isVirtual);
    }

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(QuoteProductInterface::STORE_ID);
    }

    /**
     * Set Store Id
     *
     * @param int $storeId strore id
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(QuoteProductInterface::STORE_ID, $storeId);
    }

    /**
     * Declare quote model object
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote quote
     *
     * @return $this
     */
    public function setQuote(\Appseconnect\B2BMage\Model\Quote $quote)
    {
        $this->quote = $quote;
        $this->setQuoteId($quote->getId());
        $this->setStoreId($quote->getStoreId());
        return $this;
    }

    /**
     * Retrieve product model object associated with item
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if ($product === null && $this->getProductId()) {
            $product = clone $this->productRepository->getById(
                $this->getProductId(), false, $this->getQuote()
                    ->getStoreId()
            );
            $this->setProduct($product);
        }
        
        return $product;
    }

    /**
     * Setup product for quote product
     *
     * @param \Magento\Catalog\Model\Product $product product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        if ($this->getQuote()) {
            $product->setStoreId(
                $this->getQuote()
                    ->getStoreId()
            );
            $product->setCustomerGroupId(
                $this->getQuote()
                    ->getCustomerGroupId()
            );
        }
        
        if (! $this->getId()) {
            $this->setData('product', $product)
                ->setProductId($product->getId())
                ->setProductType($product->getTypeId())
                ->setProductSku(
                    $this->getProduct()
                        ->getSku()
                )
                ->setName($product->getName())
                ->setWeight(
                    $this->getProduct()
                        ->getWeight()
                );
        }
        
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @codeCoverageIgnore
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Prepare quantity
     *
     * @param float|int $qty qty
     *
     * @return int|float
     */
    public function _prepareQty($qty)
    {
        $qty = $this->localeFormat->getNumber($qty);
        $qty = $qty > 0 ? $qty : 1;
        return $qty;
    }

    /**
     * Adding quantity to quote item
     *
     * @param int $qty qty
     *
     * @return $this
     */
    public function addQty($qty)
    {
        if (! $this->getParentItem() || ! $this->getId()) {
            $qty = $this->_prepareQty($qty);
            $this->setQtyToAdd($qty);
            $this->setQty($this->getQty() + $qty);
        }
        return $this;
    }

    /**
     * Declare quote item quantity
     *
     * @param int $qty qty
     *
     * @return $this
     */
    public function setQty($qty)
    {
        $qty = $this->_prepareQty($qty);
        $oldQty = $this->_getData(self::QTY);
        $this->setData(self::QTY, $qty);
        
        if ($this->getUseOldQty()) {
            $this->setData(self::QTY, $oldQty);
        }
        
        return $this;
    }

    /**
     * Check product representation in item
     *
     * @param \Magento\Catalog\Model\Product $product product
     *
     * @return bool
     */
    public function representProduct($product)
    {
        $itemProduct = $this->getProduct();
        if (! $product || $itemProduct->getId() != $product->getId()) {
            return false;
        }
        
        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }
        
        if ($this->getProductSku() != $product->getSku()) {
            return false;
        }
        
        return true;
    }

    /**
     * Calculate row total
     *
     * @return $this
     */
    public function calcRowTotal()
    {
        $qty = $this->getQty();
        
        $total = $this->priceCurrency->round($this->getPrice()) * $qty;
        $baseTotal = $this->priceCurrency->round($this->getBasePrice()) * $qty;
        
        $this->setRowTotal($this->priceCurrency->round($total));
        $this->setBaseRowTotal($this->priceCurrency->round($baseTotal));
        return $this;
    }
}
