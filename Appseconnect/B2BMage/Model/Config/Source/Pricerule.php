<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Config\Source;

/**
 * Class Pricerule
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Pricerule implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' =>'price_list', 'label' => __('Price List')],
            ['value' => 'tier_price', 'label' => __('Tier Price')],
            ['value' =>'special_price', 'label' => __('Special Price')],
            ['value' =>'category_price', 'label' => __('Category Price')],
            ['value' =>'item_group_price', 'label' => __('Item Group Price')]
        ];
    }
}
