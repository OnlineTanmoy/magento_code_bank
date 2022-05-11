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
namespace Appseconnect\B2BMage\Model\ResourceModel\Quote;

use Magento\Framework\App\ObjectManager;

/**
 * Class CollectionFactory
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CollectionFactory implements CollectionFactoryInterface
{

    /**
     * Instance name to create
     *
     * @var string
     */
    public $instanceName = null;

    /**
     * Factory constructor
     *
     * @param string $instanceName InstanceName
     */
    public function __construct(
        $instanceName = '\\Appseconnect\\B2BMage\\Model\\ResourceModel\\Quote\\Collection'
    ) {
        $this->instanceName = $instanceName;
    }

    /**
     * Create
     *
     * @param null $customerId      CustomerId
     * @param null $contactPersonId ContactPersonId
     *
     * @return Collection|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function create($customerId = null, $contactPersonId = null)
    {
        $collection = ObjectManager::getInstance()->create($this->instanceName);
        
        if ($customerId && $contactPersonId) {
            $collection->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('contact_id', $contactPersonId);
        } elseif ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        } elseif ($contactPersonId) {
            $collection->addFieldToFilter('contact_id', $contactPersonId);
        }
        
        return $collection;
    }
}
