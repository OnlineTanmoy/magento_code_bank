<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Adminhtml\Category;

/**
 * Class Save
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
    /**
     * The list of inputs that need to convert from string to boolean
     *
     * @var array
     */
    protected $stringToBoolInputs = [
        'custom_use_parent_settings',
        'custom_apply_to_products',
        'is_active',
        'include_in_menu',
        'is_anchor',
        'use_default' => ['url_key'],
        'use_config' => [
            'available_sort_by',
            'customer_group',
            'filter_price_range',
            'default_sort_by'
        ]
    ];
}
