<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Quotation\Quote\Item\Renderer;

use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Appseconnect\B2BMage\Model\QuoteProduct as QuoteItem;
use Magento\Customer\Model\Session;

/**
 * Interface DefaultRenderer
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DefaultRenderer extends \Magento\Framework\View\Element\Template
{

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    public $string;
    
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;
    
    /**
     * Session
     *
     * @var Session
     */
    public $session;
    
    /**
     * Helper quote
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    public $helperQuote;
    
    /**
     * Product option
     *
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    public $productOptionFactory;

    /**
     * DefaultRenderer constructor.
     *
     * @param Session                                          $customerSession      customer session
     * @param \Magento\Framework\View\Element\Template\Context $context              context
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data      $helperQuote          helper quote
     * @param \Magento\Framework\Stdlib\StringUtils            $string               string
     * @param \Magento\Catalog\Model\Product\OptionFactory     $productOptionFactory product option
     * @param array                                            $data                 data
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuote,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        array $data = []
    ) {
        $this->string = $string;
        $this->session = $customerSession;
        $this->helperQuote = $helperQuote;
        $this->productOptionFactory = $productOptionFactory;
        parent::__construct($context, $data);
    }



    /**
     * Set item
     *
     * @param \Magento\Framework\DataObject $item item
     *
     * @return $this
     */
    public function setItem(\Magento\Framework\DataObject $item)
    {
        $this->setData('item', $item);
        return $this;
    }
    
    /**
     * Get catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\Session::class
        );
        return $this->catalogSession;
    }

    /**
     * Get Item
     *
     * @return array|null
     */
    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->getQuoteItem()->getQuote();
    }

    /**
     * Get quote item
     *
     * @return array|null
     */
    public function getQuoteItem()
    {
        if ($this->getItem() instanceof \Appseconnect\B2BMage\Model\QuoteProduct) {
            return $this->getItem();
        } else {
            return $this->getItem()->getQuoteItem();
        }
    }

    /**
     * Get item option
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        $orderItem = $this->getOrderItem();
        $options = $orderItem->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Return sku of quote item.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getItem()->getProductSku();
    }

    /**
     * Return product additional information block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        $additionalProductInfoBlock = $this->getLayout()->getBlock('additional.product.info');
        return $additionalProductInfoBlock;
    }

    /**
     * Prepare SKU
     *
     * @param string $sku sku
     *
     * @return string
     */
    public function prepareSku($sku)
    {
        $formattedSku = $this->escapeHtml($this->string->splitInjection($sku));
        return $formattedSku;
    }

    /**
     * Return item unit price html
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item item
     *
     * @return string
     */
    public function getItemPriceHtml($item = null)
    {
        $priceBlock = $this->getLayout()->getBlock('item_unit_price');
        if (! $item) {
            $item = $this->getItem();
        }
        $priceBlock->setItem($item);
        return $priceBlock->toHtml();
    }

    /**
     * Return item row total html
     *
     * @param QuoteItem $item item
     *
     * @return string
     */
    public function getItemRowTotalHtml($item = null)
    {
        $rowBlock = $this->getLayout()->getBlock('item_row_total');
        if (! $item) {
            $item = $this->getItem();
        }
        $rowBlock->setItem($item);
        return $rowBlock->toHtml();
    }

    /**
     * Return the total amount minus discount
     *
     * @param QuoteItem $item item
     *
     * @return mixed
     */
    public function getTotalAmount($item)
    {
        $totalAmount = ($item->getRowTotal()
                        + $item->getTaxAmount()
                        + $item->getDiscountTaxCompensationAmount()
                        + $item->getWeeeTaxAppliedRowAmount()
                        - $item->getDiscountAmount());
        
        return $totalAmount;
    }

    /**
     * Get remove url
     *
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $item item
     *
     * @return string
     */
    public function getRemoveUrl($item)
    {
        $itemId = $item->getId();
        return $this->getUrl(
            'b2bmage/quotation/index_delete', [
            'item_id' => $itemId
            ]
        );
    }

    /**
     * Return HTML for item total after discount
     *
     * @param QuoteItem $item item
     *
     * @return string
     */
    public function getItemRowTotalAfterDiscountHtml($item = null)
    {
        $discountBlock = $this->getLayout()->getBlock('item_row_total_after_discount');
        if (! $item) {
            $item = $this->getItem();
        }
        $discountBlock->setItem($item);
        return $discountBlock->toHtml();
    }

    /**
     * Get salesrep id
     *
     * @return NULL|int
     */
    public function getSalesrepId()
    {
        $salesrepId = $this->getCatalogSession()->getSalesrepId();
        return $salesrepId ? $salesrepId : null;
    }

    /**
     * Get delete post json
     *
     * @param array $item item
     *
     * @return string
     */
    public function getDeletePostJson($item)
    {
        return $this->helperQuote->getDeletePostJson($item);
    }
}
