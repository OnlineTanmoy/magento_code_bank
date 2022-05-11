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

/**
 * Class History
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class History extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    public $salesData = null;

    /**
     * Admin
     *
     * @var \Magento\Sales\Helper\Admin
     */
    private $adminHelper;

    /**
     * History constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context     Context
     * @param \Magento\Sales\Helper\Data              $salesData   SalesData
     * @param \Magento\Framework\Registry             $registry    Registry
     * @param \Magento\Sales\Helper\Admin             $adminHelper AdminHelper
     * @param array                                   $data        Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->salesData = $salesData;
        parent::__construct($context, $data);
        $this->adminHelper = $adminHelper;
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('quote_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Submit Comment'),
                'class' => 'action-save action-secondary',
                'onclick' => $onclick
                ]
            );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Get stat uses
     *
     * @return array
     */
    public function getStatuses()
    {
        $state = $this->getOrder()->getState();
        $statuses = $this->getOrder()
            ->getConfig()
            ->getStateStatuses($state);
        return $statuses;
    }

    /**
     * Check allow to send order comment email
     *
     * @return bool
     */
    public function canSendCommentEmail()
    {
        return $this->salesData->canSendOrderCommentEmail(
            $this->getOrder()
                ->getStore()
                ->getId()
        );
    }

    /**
     * Retrieve quote model
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_customer_quote');
    }

    /**
     * Check allow to add comment
     *
     * @return bool
     */
    public function canAddComment()
    {
        return $this->_authorization->isAllowed('Appseconnect_Quotation::comment')
                && $this->getQuote()->canComment();
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl(
            'b2bmage/quotation/index_addComment',
            [
            'quote_id' => $this->getQuote()
                ->getId()
            ]
        );
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param \Appseconnect\B2BMage\Model\QuoteHistory $history History
     *
     * @return bool
     */
    public function isCustomerNotificationNotApplicable(\Appseconnect\B2BMage\Model\QuoteHistory $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }

    /**
     * Replace links in string
     *
     * @param array|string $data        Data
     * @param null|array   $allowedTags AllowedTags
     *
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }
}
