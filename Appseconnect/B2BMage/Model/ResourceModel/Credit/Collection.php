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
namespace Appseconnect\B2BMage\Model\ResourceModel\Credit;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * String
     *
     * @var string
     */
    public $idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\B2BMage\Model\Credit',
            'Appseconnect\B2BMage\Model\ResourceModel\Credit'
        );
        $this->_map['fields']['id'] = 'main_table.id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }
    
    /**
     * GetCreditBalance
     *
     * @param int $incrementId IncrementId IncrementId
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Credit\Collection
     */
    public function getCreditBalance($incrementId = null)
    {
        $this->getConnection();
        if ($incrementId) {
            $this->getSelect()
                ->columns(
                    [
                    '(sum(debit_amount)-sum(credit_amount)) as dif',
                    ]
                )
                ->where('increment_id=?', $incrementId);
        }
        return $this;
    }
}
