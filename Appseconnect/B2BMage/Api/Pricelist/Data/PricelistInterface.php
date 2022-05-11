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
 * Interface PricelistInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface PricelistInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * Pricelist ID
     */
    const ID = 'id';

    /**
     * Website ID.
     */
    const WEBSITE_ID = 'website_id';

    /**
     * Pricelist Name.
     */
    const PRICELIST_NAME = 'pricelist_name';

    /**
     * Factor.
     */
    const FACTOR = 'factor';

    /**
     * Status.
     */
    const IS_ACTIVE = 'is_active';

    /**
     * Gets the id for pricelist.
     *
     * @return int|null Pricelist Id.
     */
    public function getId();

    /**
     * Gets the website id for pricelist.
     *
     * @return int|null Website Id.
     */
    public function getWebsiteId();

    /**
     * Gets pricelist name.
     *
     * @return string|null Pricelist Name.
     */
    public function getPricelistName();

    /**
     * Gets the factor.
     *
     * @return float|null Factor.
     */
    public function getFactor();

    /**
     * Gets the is active.
     *
     * @return int|null Is Active.
     */
    public function getIsActive();

    /**
     * Sets Pricelist ID.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Sets website ID.
     *
     * @param int $websiteId website id
     *
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Sets price name.
     *
     * @param string $pricelistName pricelist name
     *
     * @return $this
     */
    public function setPricelistName($pricelistName);

    /**
     * Sets factor
     *
     * @param float $factor factor
     *
     * @return $this
     */
    public function setFactor($factor);

    /**
     * Sets Is Active.
     *
     * @param int $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive);
}
