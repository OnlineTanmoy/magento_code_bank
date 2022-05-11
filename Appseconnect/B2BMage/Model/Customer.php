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
namespace Appseconnect\B2BMage\Model;

use Appseconnect\B2BMage\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class Customer
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Customer extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Special price collection
     *
     * @var CollectionFactory
     */
    public $specialPriceCollectionFactory;

    /**
     * Customer constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context                       context
     * @param CollectionFactory                                            $specialPriceCollectionFactory special price collection
     * @param \Magento\Framework\Registry                                  $registry                      registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource                      resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection            resource collection
     * @param array                                                        $data                          data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        CollectionFactory $specialPriceCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->specialPriceCollectionFactory = $specialPriceCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\Customer');
    }

    /**
     * Is customer already assigned
     *
     * @param $data data
     *
     * @return bool
     */
    public function isCustomerAlreadyAssigned($data)
    {
        $collection = $this->specialPriceCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $data['customer_id']);
        if (isset($data['id']) && !empty($data['id'])) {
            $collection->addFieldToFilter('id', ['nin' => $data['id']]);
        }
        $collection->getSelect()
            ->where(
                'main_table.start_date = "'.$data['start_date']. '" or
                main_table.end_date = "'.$data['end_date'].'"'
            );
        if ($collection->getData()) {
            return true;
        }
        return false;
    }
}
