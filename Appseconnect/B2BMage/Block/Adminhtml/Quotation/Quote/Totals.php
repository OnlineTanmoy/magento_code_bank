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
 * Class Totals
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Totals extends \Appseconnect\B2BMage\Block\Adminhtml\Quotation\Totals
{

    /**
     * PrepareLayout
     *
     * @return mixed
     */
    public function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('quote_totals_block').parentNode, '" . $this->getUpdateUrl() . "')";
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Update Subtotal'),
                'class' => 'action-save action-secondary',
                'onclick' => $onclick
                ]
            );
        $this->setChild('update_subtotal', $button);
        return parent::_prepareLayout();
    }

    /**
     * Initialize quote totals array
     *
     * @return $this
     */
    public function _initTotals()
    {
        parent::_initTotals();
        $this->totals['paid'] = $this->dataObjectFactory->create(
            [
            'code' => 'paid',
            'strong' => true,
            'value' => $this->getSource()
                ->getTotalPaid(),
            'base_value' => $this->getSource()
                ->getBaseTotalPaid(),
            'label' => __('Total Paid'),
            'area' => 'footer'
            ]
        );
        $this->totals['refunded'] = $this->dataObjectFactory->create(
            [
            'code' => 'refunded',
            'strong' => true,
            'value' => $this->getSource()
                ->getTotalRefunded(),
            'base_value' => $this->getSource()
                ->getBaseTotalRefunded(),
            'label' => __('Total Refunded'),
            'area' => 'footer'
            ]
        );
        $this->totals['due'] = $this->dataObjectFactory->create(
            [
            'code' => 'due',
            'strong' => true,
            'value' => $this->getSource()
                ->getTotalDue(),
            'base_value' => $this->getSource()
                ->getBaseTotalDue(),
            'label' => __('Total Due'),
            'area' => 'footer'
            ]
        );
        return $this;
    }

    /**
     * Update URL getter
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl(
            'b2bmage/quotation/index_updateSubtotal',
            [
            'quote_id' => $this->getQuote()
                ->getId()
            ]
        );
    }
}
