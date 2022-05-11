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

/**
 * Class ContactPerson
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ContactPerson extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\ContactPerson\Data\ContactPersonInterface
{

    /**
     * Get customer id
     *
     * @return \Appseconnect\B2BMage\Api\ContactPerson\Data\CustomerExtendInterface[]|null
     */
    public function getContactperson()
    {
        return $this->_get(self::CONTACTPERSON);
    }

    /**
     * Set customer id
     *
     * @param \Appseconnect\B2BMage\Api\ContactPerson\Data\CustomerExtendInterface[] $contactperson Contactperson
     *
     * @return $this
     */
    public function setContactperson(array $contactperson = null)
    {
        return $this->setData(self::CONTACTPERSON, $contactperson);
    }
}
