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
namespace Appseconnect\B2BMage\Api\Pricelist\Data;

/**
 * Interface EntityInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface EntityInterface
{

    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    
    /**
     * Pricelist items
     */
    const ITEMS = 'items';

    /**
     * ID
     */
    const ID = 'id';

    /**
     * Get Pricelist Items.
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[]|null
     */
    public function getItems();

    /**
     * Gets the id for pricelist.
     *
     * @return int|null Pricelist Id.
     */
    public function getId();

    /**
     * Sets Pricelist Items.
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface[] $pricelist pricelist
     *
     * @return $this
     */
    public function setItems(array $pricelist = null);

    /**
     * Sets Pricelist ID.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);
}
