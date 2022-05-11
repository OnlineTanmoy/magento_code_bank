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

namespace Appseconnect\B2BMage\Model\Data;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class AddressData
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AddressData extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Sales\Data\AddressDataInterface
{

    /**
     * Get Firstname.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRTSNAME);
    }

    /**
     * Set Firstname.
     *
     * @param string $firstname Firstname
     *
     * @return $this
     */
    public function setFirstname($firstname = null)
    {
        return $this->setData(self::FIRTSNAME, $firstname);
    }

    /**
     * Get Lastname.
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * Set Lastname.
     *
     * @param string $lastname Lastname
     *
     * @return $this
     */
    public function setLastname($lastname = null)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get Middlename.
     *
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->_get(self::MIDDLENAME);
    }

    /**
     * Set Middlename.
     *
     * @param string $middlename Middlename
     *
     * @return $this
     */
    public function setMiddlename($middlename = null)
    {
        return $this->setData(self::MIDDLENAME, $middlename);
    }

    /**
     * Get Street.
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->_get(self::STREET);
    }

    /**
     * Set Street.
     *
     * @param string $street Street
     *
     * @return $this
     */
    public function setStreet($street = null)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * Get City.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set City.
     *
     * @param string $city City
     *
     * @return $this
     */
    public function setCity($city = null)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get Country Id.
     *
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->_get(self::COUNTRY_ID);
    }

    /**
     * Set Country Id.
     *
     * @param string $countryId CountryId
     *
     * @return $this
     */
    public function setCountryId($countryId = null)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * Get Region.
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->_get(self::REGION);
    }

    /**
     * Set Region.
     *
     * @param string $region Region
     *
     * @return $this
     */
    public function setRegion($region = null)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Get Telephone.
     *
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get(self::TELEPHONE);
    }

    /**
     * Set Telephone.
     *
     * @param string $telephone Telephone
     *
     * @return $this
     */
    public function setTelephone($telephone = null)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Get Postcode.
     *
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get(self::POSTCODE);
    }

    /**
     * Set Postcode.
     *
     * @param string $postcode Postcode
     *
     * @return $this
     */
    public function setPostcode($postcode = null)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Get Fax.
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->_get(self::FAX);
    }

    /**
     * Set Fax.
     *
     * @param string $fax Fax
     *
     * @return $this
     */
    public function setFax($fax = null)
    {
        return $this->setData(self::FAX, $fax);
    }

    /**
     * Get Save is Address Book.
     *
     * @return int|null
     */
    public function getSaveInAddresBook()
    {
        return $this->_get(self::SAVE_IN_ADDRESS_BOOK);
    }

    /**
     * Set Save In Addres Book.
     *
     * @param int $saveInAddresBook SaveInAddresBook
     *
     * @return $this
     */
    public function setSaveInAddresBook($saveInAddresBook = null)
    {
        return $this->setData(self::SAVE_IN_ADDRESS_BOOK, $saveInAddresBook);
    }
}
