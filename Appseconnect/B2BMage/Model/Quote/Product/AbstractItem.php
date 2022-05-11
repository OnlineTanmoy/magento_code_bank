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
namespace Appseconnect\B2BMage\Model\Quote\Product;

use Appseconnect\B2BMage\Model\QuoteProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Abstract Class AbstractItem
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class AbstractItem extends \Magento\Framework\Model\AbstractModel
{
    /**
     * QuoteProduct
     *
     * @var QuoteProduct|null
     */
    public $parentItem = null;

    /**
     * AbstractItem[]
     *
     * @var \Appseconnect\B2BMage\Model\Quote\Product\AbstractItem[]
     */
    public $children = [];
    
    /**
     * ProductRepositoryInterface
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    
    /**
     * PriceCurrencyInterface
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;
    
    /**
     * FormatInterface
     *
     * @var \Magento\Framework\Locale\FormatInterface
     */
    public $localeFormat;

    /**
     * AbstractItem constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context            Context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface            $priceCurrency      PriceCurrency
     * @param \Magento\Framework\Locale\FormatInterface                    $localeFormat       LocaleFormat
     * @param ProductRepositoryInterface                                   $productRepository  ProductRepository
     * @param \Magento\Framework\Registry                                  $registry           Registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource           Resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection ResourceCollection
     * @param array                                                        $data               Data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->localeFormat = $localeFormat;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set parent item
     *
     * @param QuoteProduct $parentItem ParentItem
     *
     * @return $this
     */
    public function setParentItem($parentItem)
    {
        if ($parentItem) {
            $this->parentItem = $parentItem;
            $parentItem->addChild($this);
        }
        return $this;
    }

    /**
     * Specify parent item id before saving data
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if ($this->getParentItem()) {
            $this->setParentItemId(
                $this->getParentItem()
                    ->getId()
            );
        }
        return $this;
    }

    /**
     * Get parent item
     *
     * @return QuoteProduct
     */
    public function getParentItem()
    {
        return $this->parentItem;
    }

    /**
     * Get child items
     *
     * @return \Appseconnect\B2BMage\Model\Quote\Product\AbstractItem[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add child item
     *
     * @param \Appseconnect\B2BMage\Model\Quote\Product\AbstractItem $child Child
     *
     * @return $this
     */
    public function addChild($child)
    {
        $this->setHasChildren(true);
        $this->children[] = $child;
        return $this;
    }
}
