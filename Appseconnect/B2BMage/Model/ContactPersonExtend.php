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

use Magento\Customer\Model\Data\Customer;
use \Appseconnect\B2BMage\Api\ContactPerson\Data\ContactPersonExtendInterface;

/**
 * Class ContactPersonExtend
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ContactPersonExtend extends Customer implements ContactPersonExtendInterface
{

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Contact Person id
     *
     * @return int|null
     */
    public function getContactPersonId()
    {
        return $this->_get(self::CONTACTPERSON_ID);
    }

    /**
     * Set Contact Person id
     *
     * @param int $contactPersonId contact person id
     * 
     * @return $this
     */
    public function setContactPersonId($contactPersonId)
    {
        return $this->setData(self::CONTACTPERSON_ID, $contactPersonId);
    }
}
