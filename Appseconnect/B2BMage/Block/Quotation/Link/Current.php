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
namespace Appseconnect\B2BMage\Block\Quotation\Link;

use Magento\Customer\Model\Session;

/**
 * Interface AbstractItems
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Current extends \Magento\Framework\View\Element\Template
{

    /**
     * Default path
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    public $defaultPath;

    /**
     * Phrase
     *
     * @var \Magento\Framework\PhraseFactory
     */
    public $phraseFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Quote helper
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    public $quoteHelper;

    /**
     * Helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helper;

    /**
     * Current constructor.
     *
     * @param Session                                          $customerSession customer session
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data      $quoteHelper     quote helper
     * @param \Magento\Framework\PhraseFactory                 $phraseFactory   phrase
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param \Magento\Framework\App\DefaultPathInterface      $defaultPath     default path
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data  $helper          helper
     * @param array                                            $data            data
     */
    public function __construct(
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\Quotation\Data $quoteHelper,
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helper,
        \Appseconnect\B2BMage\Helper\Quotation\Data $helperQuote,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->phraseFactory = $phraseFactory;
        $this->customerSession = $customerSession;
        $this->quoteHelper = $quoteHelper;
        $this->defaultPath = $defaultPath;
        $this->helper = $helper;
        $this->helperQuote= $helperQuote;
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath());
    }

    /**
     * Get current mca
     *
     * @return string
     */
    private function _getMca()
    {
        $routeParts = [
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName()
        ];

        $parts = [];
        foreach ($routeParts as $key => $value) {
            if ((! empty($value)
                && $value != $this->defaultPath->getPart($key))
                || ($key == 'controller' && $value == 'index')
            ) {
                $parts[] = $value;
            }
        }
        return implode('/', $parts);
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getCurrent() || $this->getUrl($this->getPath()) == $this->getUrl($this->_getMca());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $isAdministrator = $this->helper->isAdministrator($this->customerSession->getCustomerId());
        $isQuotationEnabledForCustomer=$this->helperQuote->getEnableQuoteValue();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $isQuotationEnabled = $this->quoteHelper->isQuotationEnabled();
        if (false != $this->getTemplate() || ($customerType != 3 || ! $isQuotationEnabled) || $isAdministrator == 3 ||$isQuotationEnabledForCustomer==0 ) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>' . $this->escapeHtml(
                (string) $this->phraseFactory->create(
                    [
                    'text' => $this->getLabel()
                    ]
                )
            ) . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle() ? ' title="' . $this->escapeHtml(
                (string) $this->phraseFactory->create(
                    [
                    'text' => $this->getLabel()
                    ]
                )
            ) . '"' : '';
            $html .= $this->_getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->escapeHtml(
                (string) $this->phraseFactory->create(
                    [
                    'text' => $this->getLabel()
                    ]
                )
            );

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }
        return $html;
    }

    /**
     * Generate attributes' HTML code
     *
     * @return string
     */
    private function _getAttributesHtml()
    {
        $attributesHtml = '';
        $attributes = $this->getAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute => $value) {
                $attributesHtml .= ' ' . $attribute . '="' . $this->escapeHtml($value) . '"';
            }
        }

        return $attributesHtml;
    }
}
