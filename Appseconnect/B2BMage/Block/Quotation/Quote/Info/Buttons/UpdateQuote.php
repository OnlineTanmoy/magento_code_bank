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
namespace Appseconnect\B2BMage\Block\Quotation\Quote\Info\Buttons;

/**
 * Interface UpdateQuote
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdateQuote extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Http context
     *
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    /**
     * UpdateQuote constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context     context
     * @param \Magento\Framework\Registry                      $registry    registry
     * @param \Magento\Framework\App\Http\Context              $httpContext http context
     * @param array                                            $data        data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     *  To html
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::quotation/quote/info/buttons/update_quote.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_current_customer_quote');
    }

    /**
     * Get upadet quote url
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote quote
     *
     * @return string
     */
    public function getUpdateQuoteUrl($quote)
    {
        return $this->getUrl(
            'b2bmage/quotation/index_update', [
            'quote_id' => $quote->getId()
            ]
        );
    }
}
