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
namespace Appseconnect\B2BMage\Api\ContactPerson\Data;

/**
 * Interface ContactPersonInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ContactPersonExtendInterface extends \Magento\Customer\Api\Data\CustomerInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_ID = 'customer_id';

    const CONTACTPERSON_ID = 'contactperson_id';

    /**
     * #@-
     */
    
    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer id
     *
     * @param $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Contact Person id
     *
     * @return int|null
     */
    public function getContactPersonId();

    /**
     * Set Contact Person id
     *
     * @param $contactPersonId contact person id
     * 
     * @return $this
     */
    public function setContactPersonId($contactPersonId);
}
