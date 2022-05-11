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
 * Class Entity
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Entity extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Pricelist\Data\EntityInterface
{
    /**
     * Get Pricelist Items
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[]|null
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }
    /**
     * Set Pricelist Items
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[] $pricelist Pricelist
     *
     * @return $this
     */
    public function setItems(array $pricelist = null)
    {
        return $this->setData(self::ITEMS, $pricelist);
    }
    
    /**
     * Set pricelist id
     *
     * @param int $id Id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
    
    /**
     * Get pricelist id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }
}
