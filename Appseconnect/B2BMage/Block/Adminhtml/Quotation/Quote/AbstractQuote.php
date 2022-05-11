<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote;

use Appseconnect\B2BMage\Model\Quote;

/**
 * Class AbstractQuote
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AbstractQuote extends \Magento\Backend\Block\Widget
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    public $adminHelper;

    /**
     * AbstractQuote constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context     Context
     * @param \Magento\Framework\Registry             $registry    Registry
     * @param \Magento\Sales\Helper\Admin             $adminHelper AdminHelper
     * @param array                                   $data        Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->adminHelper = $adminHelper;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve available quote
     *
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuote()
    {
        if ($this->hasQuote()) {
            return $this->getData('quote');
        }
        if ($this->coreRegistry->registry('insync_current_customer_quote')) {
            return $this->coreRegistry->registry('insync_current_customer_quote');
        }
        if ($this->coreRegistry->registry('quote')) {
            return $this->coreRegistry->registry('quote');
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('We can\'t get the quote instance right now.')
        );
    }

    /**
     * Get price data object
     *
     * @return Quote|mixed
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getQuote();
        }
        return $obj;
    }

    /**
     * Display price attribute
     *
     * @param string $code      Code
     * @param bool   $strong    Strong
     * @param string $separator Separator
     *
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->adminHelper
            ->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    /**
     * Display prices
     *
     * @param float  $basePrice BasePrice
     * @param float  $price     Price
     * @param bool   $strong    Strong
     * @param string $separator Separator
     *
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->adminHelper
            ->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    /**
     * Retrieve quote totals block settings
     *
     * @return array
     */
    public function getQuoteTotalData()
    {
        return [];
    }

    /**
     * Retrieve quote info block settings
     *
     * @return array
     */
    public function getQuoteInfoData()
    {
        return [];
    }

    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param \Magento\Framework\DataObject $quote Quote
     *
     * @return string
     */
    public function displayShippingPriceInclTax($quote)
    {
        $shipping = $quote->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $quote->getBaseShippingInclTax();
        } else {
            $shipping = $quote->getShippingAmount() + $quote->getShippingTaxAmount();
            $baseShipping = $quote->getBaseShippingAmount() + $quote->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
}
