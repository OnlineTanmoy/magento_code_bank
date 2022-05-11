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

use Magento\Framework\Api\AttributeValueFactory;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteStatusHistoryInterface;
use Magento\Sales\Model\AbstractModel;

/**
 * Class QuoteHistory
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteHistory extends AbstractModel implements QuoteStatusHistoryInterface
{

    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Quote instance
     *
     * @var \Appseconnect\B2BMage\Model\Quote
     */
    public $quote;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * QuoteHistory constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context                context
     * @param \Magento\Framework\Registry                                  $registry               registery
     * @param \Magento\Framework\Api\ExtensionAttributesFactory            $extensionFactory       extension factory
     * @param AttributeValueFactory                                        $customAttributeFactory customer attribute
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager           store manager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource               resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection     resource collection
     * @param array                                                        $data                   data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory');
    }

    /**
     * Set quote object and grab some metadata from it
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote quote
     *
     * @return $this
     */
    public function setQuote(\Appseconnect\B2BMage\Model\Quote $quote)
    {
        $this->quote = $quote;
        $this->setStoreId($quote->getStoreId());
        return $this;
    }

    /**
     * Notification flag
     *
     * @param mixed $flag flag
     *
     * @return $this
     */
    public function setIsCustomerNotified($flag = null)
    {
        if ($flag === null) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }
        
        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Get quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Retrieve status label
     *
     * @return string|null
     */
    public function getStatusLabel()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getStatusLabel();
        }
        return null;
    }

    /**
     * Get store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getStore();
        }
        return $this->storeManager->getStore();
    }

    /**
     * Set quote again if required
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        
        if (! $this->getParentId() && $this->getQuote()) {
            $this->setParentId(
                $this->getQuote()
                    ->getId()
            );
        }
        
        return $this;
    }

    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->getData(QuoteStatusHistoryInterface::COMMENT);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(QuoteStatusHistoryInterface::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $createdAt created at
     *
     * @return QuoteHistory
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(QuoteStatusHistoryInterface::CREATED_AT, $createdAt);
    }

    /**
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(QuoteStatusHistoryInterface::ENTITY_ID);
    }

    /**
     * Returns entity_name
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->getData(QuoteStatusHistoryInterface::ENTITY_NAME);
    }

    /**
     * Returns is_customer_notified
     *
     * @return int
     */
    public function getIsCustomerNotified()
    {
        return $this->getData(QuoteStatusHistoryInterface::IS_CUSTOMER_NOTIFIED);
    }

    /**
     * Returns is_visible_on_front
     *
     * @return int
     */
    public function getIsVisibleOnFront()
    {
        return $this->getData(QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT);
    }

    /**
     * Returns name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(QuoteStatusHistoryInterface::NAME);
    }

    /**
     * Returns parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(QuoteStatusHistoryInterface::PARENT_ID);
    }

    /**
     * Returns status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(QuoteStatusHistoryInterface::STATUS);
    }

    /**
     * Set parent id
     *
     * @param int $id id
     *
     * @return QuoteHistory
     */
    public function setParentId($id)
    {
        return $this->setData(QuoteStatusHistoryInterface::PARENT_ID, $id);
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return QuoteHistory
     */
    public function setName($name)
    {
        return $this->setData(QuoteStatusHistoryInterface::NAME, $name);
    }

    /**
     * Set is visable on front
     *
     * @param int $isVisibleOnFront is visible on front
     *
     * @return QuoteHistory
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->setData(QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }

    /**
     * Set comment
     *
     * @param string $comment comment
     *
     * @return QuoteHistory
     */
    public function setComment($comment)
    {
        return $this->setData(QuoteStatusHistoryInterface::COMMENT, $comment);
    }

    /**
     * Set status
     *
     * @param string $status status
     *
     * @return QuoteHistory
     */
    public function setStatus($status)
    {
        return $this->setData(QuoteStatusHistoryInterface::STATUS, $status);
    }

    /**
     * Set entity name
     *
     * @param string $entityName entity name
     *
     * @return QuoteHistory
     */
    public function setEntityName($entityName)
    {
        return $this->setData(QuoteStatusHistoryInterface::ENTITY_NAME, $entityName);
    }
}
