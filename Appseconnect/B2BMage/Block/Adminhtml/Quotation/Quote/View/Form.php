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
 * Class Form
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Form extends \Magento\Backend\Block\Template
{

    /**
     * Tohtml
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::quotation/quote/view/form.phtml");
        
        return parent::_toHtml();
    }
}
