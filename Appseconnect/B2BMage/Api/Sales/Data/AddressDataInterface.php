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
namespace Appseconnect\B2BMage\Api\Sales\Data;

/**
 * Interface AddressDataInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface AddressDataInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
* #@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const FIRTSNAME = 'firstname';
    
    const MIDDLENAME = 'middlename';

    const LASTNAME = 'lastname';
    
    const STREET = 'street';
    
    const CITY = 'city';
    
    const COUNTRY_ID = 'country_id';
    
    const REGION = 'region';
    
    const TELEPHONE = 'telephone';
    
    const POSTCODE = 'postcode';
    
    const FAX = 'fax';
    
    const SAVE_IN_ADDRESS_BOOK = 'save_in_address_book';
    /**
     * #@-
     */

    /**
     * Get Firstname.
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set Firstname.
     *
     * @param string $firstname firstname
     *
     * @return $this
     */
    public function setFirstname($firstname = null);

    /**
     * Get Lastname.
     *
     * @return string|null
     */
    public function getLastname();

    /**
     * Set Lastname.
     *
     * @param string $lastname lastname
     *
     * @return $this
     */
    public function setLastname($lastname = null);
    
    /**
     * Get Middlename.
     *
     * @return string|null
     */
    public function getMiddlename();
    
    /**
     * Set Middlename.
     *
     * @param string $middlename middlename
     *
     * @return $this
     */
    public function setMiddlename($middlename = null);
    
    /**
     * Get Street.
     *
     * @return string|null
     */
    public function getStreet();
    
    /**
     * Set Street.
     *
     * @param string $street street
     *
     * @return $this
     */
    public function setStreet($street = null);
    
    /**
     * Get City.
     *
     * @return string|null
     */
    public function getCity();
    
    /**
     * Set City.
     *
     * @param string $city city
     *
     * @return $this
     */
    public function setCity($city = null);
    
    /**
     * Get Country Id.
     *
     * @return string|null
     */
    public function getCountryId();
    
    /**
     * Set Country Id.
     *
     * @param string $countryId country id
     *
     * @return $this
     */
    public function setCountryId($countryId = null);
    
    /**
     * Get Region.
     *
     * @return string|null
     */
    public function getRegion();
    
    /**
     * Set Region.
     *
     * @param string $region region
     *
     * @return $this
     */
    public function setRegion($region = null);
    
    /**
     * Get Telephone.
     *
     * @return string|null
     */
    public function getTelephone();
    
    /**
     * Set Telephone.
     *
     * @param string $telephone telephone
     *
     * @return $this
     */
    public function setTelephone($telephone = null);
    
    /**
     * Get Postcode.
     *
     * @return string|null
     */
    public function getPostcode();
    
    /**
     * Set Postcode.
     *
     * @param string $postcode postcode
     *
     * @return $this
     */
    public function setPostcode($postcode = null);
    
    /**
     * Get Fax.
     *
     * @return string|null
     */
    public function getFax();
    
    /**
     * Set Fax.
     *
     * @param string $fax fax
     *
     * @return $this
     */
    public function setFax($fax = null);
    
    /**
     * Get Save is Address Book.
     *
     * @return int|null
     */
    public function getSaveInAddresBook();
    
    /**
     * Set Save In Addres Book.
     *
     * @param int $saveInAddresBook save in addres book
     *
     * @return $this
     */
    public function setSaveInAddresBook($saveInAddresBook = null);
}
