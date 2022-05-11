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
namespace Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data;

/**
 * Interface SpecialPriceInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface SpecialPriceInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * Special price ID
     */
    const ID = 'id';

    /**
     * Website ID.
     */
    const WEBSITE_ID = 'website_id';

    /**
     * Customer Id.
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * Customer name.
     */
    const CUSTOMER_NAME = 'customer_name';

    /**
     * Pricelist id.
     */
    const PRICELIST_ID = 'pricelist_id';

    /**
     * Discount type.
     */
    const DISCOUNT_TYPE = 'discount_type';

    /**
     * Start date.
     */
    const START_DATE = 'start_date';

    /**
     * End date.
     */
    const END_DATE = 'end_date';

    /**
     * Is active.
     */
    const IS_ACTIVE = 'is_active';

    /**
     * Product detail
     */
    const PRODUCT_DETAILS = 'product_details';

    /**
     * Get the id for special price.
     *
     * @return int|null Pricelist Id.
     */
    public function getId();

    /**
     * Get the website id for special price.
     *
     * @return int|null Website Id.
     */
    public function getWebsiteId();

    /**
     * Get customer id of special price.
     *
     * @return int|null Pricelist Name.
     */
    public function getCustomerId();

    /**
     * Get customer name of special price.
     *
     * @return string|null Parent Id.
     */
    public function getCustomerName();

    /**
     * Get pricelist id assign special price.
     *
     * @return int|null Discount Factor.
     */
    public function getPricelistId();

    /**
     * Get type of discount.
     *
     * @return int|null Is Active.
     */
    public function getDiscountType();

    /**
     * Get start date of special price.
     *
     * @return string|null Is Active.
     */
    public function getStartDate();

    /**
     * Get end date of special price.
     *
     * @return string|null Is Active.
     */
    public function getEndDate();

    /**
     * Gets the is active.
     *
     * @return int|null Is Active.
     */
    public function getIsActive();

    /**
     * Set the id for special price.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Set the website id for special price.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setWebsiteId($id);

    /**
     * Set customer id of special price.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setCustomerId($id);

    /**
     * Set customer name of special price.
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setCustomerName($name);

    /**
     * Set pricelist id assign special price.
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setPricelistId($id);

    /**
     * Set type of discount.
     *
     * @param int $type type
     *
     * @return $this
     */
    public function setDiscountType($type);

    /**
     * Set start date of special price.
     *
     * @param string $date date
     *
     * @return $this
     */
    public function setStartDate($date);

    /**
     * Set end date of special price.
     *
     * @param string $date date
     *
     * @return $this
     */
    public function setEndDate($date);

    /**
     * Sets the is active.
     *
     * @param int $status status
     * 
     * @return $this
     */
    public function setIsActive($status);

    /**
     * Get Product details.
     *
     * @return \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface[] |null
     */
    public function getProductDetails();

    /**
     * Sets Product Sku.
     *
     * @param \Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterface[] $productDetails product details
     *
     * @return $this
     */
    public function setProductDetails(array $productDetails = null);
}
