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
namespace Appseconnect\B2BMage\Block\Quotation\Quote\Info;

use Magento\Customer\Model\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;

/**
 * Interface Buttons
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Buttons extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;
    
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Http context
     *
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Buttons constructor.
     *
     * @param Session                                          $customerSession customer session
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param \Magento\Framework\Registry                      $registry        registry
     * @param \Magento\Framework\App\Http\Context              $httpContext     http context
     * @param array                                            $data            data
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * To html
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::quotation/quote/info/buttons.phtml");
        
        return parent::_toHtml();
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
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_current_customer_quote');
    }

    /**
     * Get url for printing order
     *
     * @param \Magento\Sales\Model\Order $order order
     *
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (! $this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl(
                'sales/guest/print', [
                'order_id' => $order->getId()
                ]
            );
        }
        return $this->getUrl(
            'sales/order/print', [
            'order_id' => $order->getId()
            ]
        );
    }

    /**
     * Get submit url
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote quote
     *
     * @return string
     */
    public function getSubmitUrl($quote)
    {
        return $this->getUrl(
            'b2bmage/quotation/index_submit', [
            'quote_id' => $quote->getId()
            ]
        );
    }

    /**
     * Get salesrep id
     *
     * @return NULL|int
     */
    public function getSalesrepId()
    {
        return $this->getCatalogSession()->getSalesrepId() ? $this->getCatalogSession()->getSalesrepId() : null;
    }

    /**
     * Get url for reorder action
     *
     * @param \Magento\Sales\Model\Order $order order
     *
     * @return string
     */
    public function getReorderUrl($order)
    {
        if (! $this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl(
                'sales/guest/reorder', [
                'order_id' => $order->getId()
                ]
            );
        }
        return $this->getUrl(
            'sales/order/reorder', [
            'order_id' => $order->getId()
            ]
        );
    }
}
