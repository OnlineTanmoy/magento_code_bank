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
namespace Appseconnect\B2BMage\Model\ResourceModel\Contact;

use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;

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
            'Appseconnect\B2BMage\Model\Contact',
            'Appseconnect\B2BMage\Model\ResourceModel\Contact'
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
     * Get ContactPersonId
     *
     * @param \Magento\Framework\Api\Search\FilterGroup                 $filterGroup FilterGroup
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection  Collection
     *
     * @return string
     */
    public function getContactPersonId(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
            
            $fields = [];
            $customerId = '';
        foreach ($filterGroup->getFilters() as $filter) {
            if (trim($filter->getField()) == 'customer_id') {
                $customerId = $filter->getValue();
                continue;
            }
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = [
            'attribute' => $filter->getField(),
            $condition => $filter->getValue()
            ];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
            return $customerId;
    }
    
    /**
     * ContactFilter
     *
     * @param CustomerCollection $collection Collection
     * @param int                $customerId CustomerId
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function contactFilter($collection, $customerId)
    {
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $collection->getSelect()
            ->where("customer.customer_id = ?", $customerId)
            ->join(
                [
                'customer' => $this->_resources->getTableName('insync_contactperson')
                ], 'e.entity_id = customer.contactperson_id', [
                'contact_id' => 'id'
                ]
            );
        return $collection;
    }
}
