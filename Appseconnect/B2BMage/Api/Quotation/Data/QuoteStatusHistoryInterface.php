<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\Quotation\Data;

/**
 * Quote status history interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 *
 * @api
 * Interface QuoteSearchResultsInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuoteStatusHistoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Name.
     */
    const NAME = 'name';

    /**
     * Parent ID.
     */
    const PARENT_ID = 'parent_id';

    /**
     * Is-customer-notified flag.
     */
    const IS_CUSTOMER_NOTIFIED = 'is_customer_notified';

    /**
     * Is-visible-on-storefront flag.
     */
    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    /**
     * Comment.
     */
    const COMMENT = 'comment';

    /**
     * Status.
     */
    const STATUS = 'status';

    /**
     * Create-at timestamp.
     */
    const CREATED_AT = 'created_at';

    /**
     * Entity name.
     */
    const ENTITY_NAME = 'entity_name';

    /**
     * Gets the comment for the order status history.
     *
     * @return string Comment.
     */
    public function getComment();

    /**
     * Gets the created-at timestamp for the order status history.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Sets the created-at timestamp for the order status history.
     *
     * @param string $createdAt timestamp
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Gets the ID for the order status history.
     *
     * @return int|null Order status history ID.
     */
    public function getEntityId();

    /**
     * Gets the name for the order status history.
     *
     * @return string Name.
     */
    public function getName();

    /**
     * Sets entity ID.
     *
     * @param int $entityId entity id
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Gets the entity name for the order status history.
     *
     * @return string|null Entity name.
     */
    public function getEntityName();

    /**
     * Gets the is-customer-notified flag value for the order status history.
     *
     * @return int Is-customer-notified flag value.
     */
    public function getIsCustomerNotified();

    /**
     * Gets the is-visible-on-storefront flag value for the order status history.
     *
     * @return int Is-visible-on-storefront flag value.
     */
    public function getIsVisibleOnFront();

    /**
     * Gets the parent ID for the order status history.
     *
     * @return int Parent ID.
     */
    public function getParentId();

    /**
     * Gets the status for the order status history.
     *
     * @return string|null Status.
     */
    public function getStatus();

    /**
     * Sets the parent ID for the order status history.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setParentId($id);

    /**
     * Sets the name for the order status history.
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Sets the is-customer-notified flag value for the order status history.
     *
     * @param int $isCustomerNotified is customer notified
     * 
     * @return $this
     */
    public function setIsCustomerNotified($isCustomerNotified);

    /**
     * Sets the is-visible-on-storefront flag value for the order status history.
     *
     * @param int $isVisibleOnFront is visible on frontend
     *
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront);

    /**
     * Sets the comment for the order status history.
     *
     * @param string $comment comment
     *
     * @return $this
     */
    public function setComment($comment);

    /**
     * Sets the status for the order status history.
     *
     * @param string $status status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Sets the entity name for the order status history.
     *
     * @param string $entityName entity name
     *
     * @return $this
     */
    public function setEntityName($entityName);
}
