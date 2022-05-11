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
 * Class CategoryVisibilityOptions
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CategoryVisibilityOptions implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'all_groups',
                'label' => __('All Customer Groups')
            ],
            [
                'value' => 'group_wise_visibility',
                'label' => __(' Customer Group wise category visible')
            ]
        ];
    }
}
