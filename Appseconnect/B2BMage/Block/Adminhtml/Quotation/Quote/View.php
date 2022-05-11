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
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote;

/**
 * Class View
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * View constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context  Context
     * @param \Magento\Framework\Registry           $registry Registry
     * @param array                                 $data     Data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return                                        void @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _construct()
    {
        $this->_objectId = 'quote_id';
        $this->_controller = 'adminhtml_quotation_quote';
        $this->_mode = 'view';
        $this->_blockGroup = 'Appseconnect_B2BMage';
        
        parent::_construct();
        
        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->removeButton('save');
        $this->setId('quote_view');
        $quote = $this->getQuote();
        
        if (! $quote) {
            return;
        }
        
        if ($this->getQuote()->getStatus() == 'submitted') {
            $this->buttonList->add(
                'quote_cancel',
                [
                'label' => __('Cancel'),
                'class' => 'cancel',
                'onclick' => 'setLocation(\'' . $this->getCancelUrl() . '\')',
                'id' => 'quote-view-cancel-button',
                'data_attribute' => [
                    'url' => $this->getCancelUrl()
                ]
                ]
            );
        }
        
        if ($this->getQuote()->getStatus() == 'submitted') {
            $this->buttonList->add(
                'quote_hold',
                [
                'label' => __('Hold'),
                'class' => __('hold'),
                'onclick' => 'setLocation(\'' . $this->getHoldUrl() . '\')',
                'id' => 'quote-view-hold-button',
                'data_attribute' => [
                    'url' => $this->getHoldUrl()
                ]
                ]
            );
        }
        
        if ($this->getQuote()->getStatus() == 'holded') {
            $this->buttonList->add(
                'quote_unhold',
                [
                'label' => __('Unhold'),
                'class' => __('unhold'),
                'onclick' => 'setLocation(\'' . $this->getUnHoldUrl() . '\')',
                'id' => 'quote-view-unhold-button',
                'data_attribute' => [
                    'url' => $this->getUnHoldUrl()
                ]
                ]
            );
        }
        
        if ($this->getQuote()->getStatus() == 'submitted') {
            $this->buttonList->add(
                'quote_approve',
                [
                'label' => __('Approve'),
                'class' => 'approve primary',
                'onclick' => 'setLocation(\'' . $this->getApproveUrl() . '\')',
                'id' => 'quote-approve-button',
                'data_attribute' => [
                    'url' => $this->getApproveUrl()
                ]
                ]
            );
        }
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_customer_quote');
    }

    /**
     * Retrieve Quote Identifier
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getQuote() ? $this->getQuote()->getId() : null;
    }

    /**
     * URL getter
     *
     * @param string $params  Params
     * @param array  $params2 Params2
     *
     * @return string
     */
    public function getUrl($params = '', $params2 = [])
    {
        $params2['quote_id'] = $this->getQuoteId();
        return parent::getUrl($params, $params2);
    }

    /**
     * Cancel URL getter
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('b2bmage/quotation/actions_cancel');
    }

    /**
     * Hold URL getter
     *
     * @return string
     */
    public function getHoldUrl()
    {
        return $this->getUrl('b2bmage/quotation/actions_hold');
    }

    /**
     * Unhold URL getter
     *
     * @return string
     */
    public function getUnholdUrl()
    {
        return $this->getUrl('b2bmage/quotation/actions_unhold');
    }

    /**
     * Approve URL
     *
     * @return string
     */
    public function getApproveUrl()
    {
        return $this->getUrl('b2bmage/quotation/actions_approve');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId ResourceId
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getQuote() && $this->getQuote()->getBackUrl()) {
            return $this->getQuote()->getBackUrl();
        }
        
        return $this->getUrl('b2bmage/quotation/index_index');
    }
}
