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
namespace Appseconnect\B2BMage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\SalesSequence\Model\Manager;

/**
 * Class Quote
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Quote extends AbstractDb
{
    /**
     * Manager
     *
     * @var \Magento\SalesSequence\Model\Manager
     */
    public $sequenceManager;

    /**
     * Quote constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context                 Context
     * @param Snapshot                                          $entitySnapshot          EntitySnapshot
     * @param RelationComposite                                 $entityRelationComposite EntityRelationComposite
     * @param Manager                                           $sequenceManager         SequenceManager
     * @param null                                              $connectionName          ConnectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Manager $sequenceManager,
        $connectionName = null
    ) {
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('insync_customer_quote', 'id');
    }

    /**
     * BeforeSave
     *
     * @param \Magento\Framework\Model\AbstractModel $object Object
     *
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (! $object->getId()) {
            $store = $object->getStore();
            $name = [
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName()
            ];
            $object->setStoreName(implode(PHP_EOL, $name));
        }
        
        $isNewCustomer = ! $object->getCustomerId() || $object->getCustomerId() === true;
        if ($isNewCustomer && $object->getCustomer()) {
            $object->setCustomerId(
                $object->getCustomer()
                    ->getId()
            );
        }
        return parent::_beforeSave($object);
    }

    /**
     * Load quote data by contact identifier
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote     Quote
     * @param int                               $contactId ContactId
     *
     * @return $this
     */
    public function loadByContactId($quote, $contactId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect('contact_id', $contactId, $quote)
            ->where('status = ?', 'open')
            ->limit(1);
        
        $data = $connection->fetchRow($select);
        
        if ($data) {
            $quote->setData($data);
        }
        
        return $this;
    }
}
