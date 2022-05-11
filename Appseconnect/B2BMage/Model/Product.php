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

use Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class Product
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Product extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Tier price collection
     *
     * @var CollectionFactory
     */
    public $tierPriceCollectionFactory;

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context                    context
     * @param CollectionFactory                                            $tierPriceCollectionFactory tier price collection
     * @param \Magento\Framework\Registry                                  $registry                   registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource                   resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection         resource collection
     * @param array                                                        $data                       data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        CollectionFactory $tierPriceCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tierPriceCollectionFactory = $tierPriceCollectionFactory;
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
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\Product');
    }

    /**
     * Is customer already assign
     *
     * @param $customerId  customer id
     * @param $tierPriceId tier price id
     *
     * @return bool
     */
    public function isCustomerAlreadyAssigned($customerId, $tierPriceId)
    {
        $collection = $this->tierPriceCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        if($tierPriceId) $collection->addFieldToFilter('id', array('neq' => $tierPriceId));
        if ($collection->getData()) {
            return true;
        }
        return false;
    }
}
