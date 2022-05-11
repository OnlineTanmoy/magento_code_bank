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
 * Interface UpdatePricelistInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface UpdatePricelistInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
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
     * Parent Pricelist ID.
     */
    const PARENT_ID = 'parent_id';

    /**
     * Discount Factor.
     */
    const DISCOUNT_FACTOR = 'discount_factor';

    /**
     * Status.
     */
    const IS_ACTIVE = 'is_active';

    /**
     * Product Sku.
     */
    const PRODUCT_SKUS = 'product_skus';

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
     * Gets the parent id.
     *
     * @return string|null Parent Id.
     */
    public function getParentId();

    /**
     * Gets the discount factor.
     *
     * @return float|null Discount Factor.
     */
    public function getDiscountFactor();

    /**
     * Gets the is active.
     *
     * @return string|null Is Active.
     */
    public function getIsActive();

    /**
     * Get Product Sku.
     *
     * @return string[]|null
     */
    public function getProductSkus();

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
     * Sets parent ID.
     *
     * @param string $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Sets discount factor
     *
     * @param float $discountFactor discount factor
     *
     * @return $this
     */
    public function setDiscountFactor($discountFactor);

    /**
     * Sets Is Active.
     *
     * @param string $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Sets Product Sku.
     *
     * @param string[] $productSku product sku
     *
     * @return $this
     */
    public function setProductSkus(array $productSku = null);
}
