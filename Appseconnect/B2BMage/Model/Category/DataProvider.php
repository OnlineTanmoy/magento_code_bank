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
namespace Appseconnect\B2BMage\Model\Category;

/**
 * Class DataProvider
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    /**
     * Elements with use config setting
     *
     * @var   array
     * @since 101.0.0
     */
    protected $elementsWithUseConfigSetting = [
        'available_sort_by',
        'customer_group',
        'default_sort_by',
        'filter_price_range',
    ];


    /**
     * Category's fields default values
     *
     * @param array $result Result
     *
     * @return array
     * @since  101.0.0
     */
    public function getDefaultMetaData($result)
    {
        $result['parent']['default'] = (int)$this->request->getParam('parent');
        $result['use_config.available_sort_by']['default'] = true;
        $result['use_config.customer_group']['default'] = true;
        $result['use_config.default_sort_by']['default'] = true;
        $result['use_config.filter_price_range']['default'] = true;

        return $result;
    }
}
