<?php
namespace Appseconnect\Shoppinglist\Block\Customer\Account;

use Magento\Customer\Model\Session;

class Dashboard extends \Magento\Framework\View\Element\Template
{

    /**
     * Default path
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    public $defaultPath;

    /**
     *
     * @var \Magento\Framework\PhraseFactory
     */
    public $phraseFactory;

    /**
     *
     * @var Session
     */
    public $customerSession;

    /**
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helper;

    /**
     *
     * @var \Appseconnect\Shoppinglist\Model\CustomerProductList
     */
    public $customerProductList;

    /**
     * @param Session $customerSession
     * @param \Magento\Framework\PhraseFactory $phraseFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data $helper
     * @param array $data
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helper,
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->phraseFactory = $phraseFactory;
        $this->customerSession = $customerSession;
        $this->defaultPath = $defaultPath;
        $this->helper = $helper;
        $this->customerProductList = $customerProductList;
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
    private function getMca()
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
                || ($key == 'controller'
                    && $value == 'index')) {
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
        return $this->getCurrent() ||
            $this->getUrl($this->getPath()) == $this->getUrl($this->getMca());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $isSalesrep = $this->helper->isSalesrep($this->customerSession->getCustomerId());
        if (false != $this->getTemplate() || $isSalesrep) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>' . $this->escapeHtml((string) $this->phraseFactory->create([
                    'text' => $this->getLabel()
                ])) . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle() ? ' title="' . $this->escapeHtml((string) $this->phraseFactory->create([
                    'text' => $this->getLabel()
                ])) . '"' : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->escapeHtml((string) $this->phraseFactory->create([
                'text' => $this->getLabel()
            ]));

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
    private function getAttributesHtml()
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

    public function getCustomerProductListCollection()
    {
        return $this->customerProductList->create()->getCollection();
    }
}
