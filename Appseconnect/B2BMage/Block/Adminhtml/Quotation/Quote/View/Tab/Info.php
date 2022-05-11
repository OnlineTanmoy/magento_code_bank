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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\View\Tab;

use Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\AbstractQuote;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Info
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Info extends AbstractQuote implements TabInterface
{

    /**
     * Retrieve quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_current_customer_quote');
    }

    /**
     * Retrieve source model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getSource()
    {
        return $this->getQuote();
    }

    /**
     * Retrieve quote totals block settings
     *
     * @return array
     */
    public function getQuoteTotalData()
    {
        return [
            'can_display_total_due' => true,
            'can_display_total_paid' => true,
            'can_display_total_refunded' => true
        ];
    }

    /**
     * Get quote info data
     *
     * @return array
     */
    public function getQuoteInfoData()
    {
        return [
            'no_use_order_link' => true
        ];
    }

    /**
     * Get tracking html
     *
     * @return string
     */
    public function getTrackingHtml()
    {
        return $this->getChildHtml('quote_tracking');
    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('quote_items');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    public function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    /**
     * Get payment html
     *
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('quote_payment');
    }

    /**
     * View URL getter
     *
     * @param int $quoteId QuoteId
     *
     * @return string
     */
    public function getViewUrl($quoteId)
    {
        return $this->getUrl(
            'b2bmage/quotation/index_index',
            [
            'quote_id' => $quoteId
            ]
        );
    }

    /**
     * ######################## TAB settings #################################
     */

    /**
     * GetTabLabel
     *
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * GetTabTitle
     *
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Quote Information');
    }

    /**
     * CanShowTab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * IsHidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
