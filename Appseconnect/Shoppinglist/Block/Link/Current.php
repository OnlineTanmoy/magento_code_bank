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
namespace Appseconnect\Shoppinglist\Block\Link;

use Magento\Customer\Model\Session;
use Magento\Customer\Block\Account\SortLinkInterface;


/**
 * Interface Current
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
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Helper
     *
     * @var \Appseconnect\Shoppinglist\Helper\Mylist\Data
     */
    public $helper;

    /**
     * Current constructor.
     *
     * @param Session                                          $customerSession customer session
     * @param \Magento\Framework\PhraseFactory                 $phraseFactory   phrase
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param \Magento\Framework\App\DefaultPathInterface      $defaultPath     default path
     * @param \Appseconnect\Shoppinglist\Helper\Mylist\Data  $helper          helper
     * @param array                                            $data            data
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Appseconnect\Shoppinglist\Helper\Mylist\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->phraseFactory = $phraseFactory;
        $this->customerSession = $customerSession;
        $this->defaultPath = $defaultPath;
        $this->helper = $helper;
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
                || ($key == 'controller'
                    && $value == 'index')
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
        $shoppinglistEnable = $this->helper->getEnableshoppinglistValue();
        if ($shoppinglistEnable == false) {
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
    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

}