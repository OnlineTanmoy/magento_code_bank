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
interface ContactPersonInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CONTACTPERSON = 'contactperson';

    /**
     * #@-
     */
    
    /**
     * Get Contactperson.
     *
     * @return \Appseconnect\B2BMage\Api\ContactPerson\Data\CustomerExtendInterface[]|null
     */
    public function getContactperson();

    /**
     * Set Contactperson.
     *
     * @param \Appseconnect\B2BMage\Api\ContactPerson\Data\CustomerExtendInterface[] $contactperson contact person
     *
     * @return $this
     */
    public function setContactperson(array $contactperson = null);
}
