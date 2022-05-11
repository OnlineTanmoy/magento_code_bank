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
namespace Appseconnect\B2BMage\Block\Quotation\Header\Quote;

/**
 * Interface Link
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * Customer Url
     *
     * @var \Magento\Customer\Model\Url
     */
    public $customerUrl;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context     context
     * @param \Magento\Customer\Model\Url                      $customerUrl customer url
     * @param array                                            $data        data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        $this->customerUrl = $customerUrl;
        parent::__construct($context, $data);
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('b2bmage/quotation/index_history');
    }
}
