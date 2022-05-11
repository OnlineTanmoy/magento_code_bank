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

/**
 * Interface Link
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context     context
     * @param \Magento\Framework\App\DefaultPathInterface      $defaultPath default path
     * @param \Magento\Framework\Registry                      $registry    registry
     * @param array                                            $data        data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->registry = $registry;
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->registry->registry('insync_current_customer_quote');
    }


    /**
     * Get href
     *
     * @inheritdoc
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl(
            $this->getPath(), [
            'quote_id' => $this->getQuote()
                ->getId()
            ]
        );
    }

    /**
     * To html
     *
     * @inheritdoc
     *
     * @return string
     */
    public function _toHtml()
    {
        if ($this->hasKey()
            && method_exists($this->getQuote(), 'has' . $this->getKey())
            && ! $this->getQuote()->{'has' . $this->getKey()}()
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
