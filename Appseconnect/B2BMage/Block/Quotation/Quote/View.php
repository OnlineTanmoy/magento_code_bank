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
namespace Appseconnect\B2BMage\Block\Quotation\Quote;

use Magento\Customer\Model\Context;

/**
 * Interface View
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends \Magento\Framework\View\Element\Template
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
     * @var   \Magento\Framework\App\Http\Context
     * @since 100.2.0
     */
    public $httpContext;

    /**
     * View constructor.
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
     * Prepare layout
     *
     * @return void
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(
            __(
                'Quote #%1', $this->getQuote()
                    ->getId()
            )
        );
    }

    /**
     * Get payment info html
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
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
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/listing');
        }
        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return __('Back to My Quotes');
        }
        return __('View Another Quote');
    }
}
