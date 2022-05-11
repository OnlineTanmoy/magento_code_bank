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
namespace Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Customers
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customers extends AbstractRenderer
{

    /**
     * Render
     *
     * @param DataObject $row Row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        return '<label class="data-grid-checkbox-cell-inner" >
                <input onclick="assigned_customers_id(this,' . $row->getId() . ')" 
                type="checkbox" name="product_id[]" value="' . $row->getId() . '" 
                id="assigned_customers_id_' . $row->getId() . '" 
                class="checkbox admin__control-checkbox"><label></label></label>';
    }
}
