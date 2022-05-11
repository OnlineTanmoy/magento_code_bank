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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\View;

use Magento\Backend\Model\Auth\Session;

/**
 * Class Tabs
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Tabs constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context     Context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder JsonEncoder
     * @param Session                                  $authSession AuthSession
     * @param \Magento\Framework\Registry              $registry    Registry
     * @param array                                    $data        Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Retrieve available quote
     *
     * @return \Appseconnect\B2BMage\Model\Quote
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
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('quote_view_tabs');
        $this->setDestElementId('quote_view');
        $this->setTitle(__('Quote View'));
    }
}
