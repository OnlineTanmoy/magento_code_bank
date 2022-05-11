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
namespace Appseconnect\B2BMage\Api\CustomerTierPrice\Data;

/**
 * Interface CustomerTierpriceInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CustomerTierpriceInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
* #@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const WEBSITE_ID = 'website_id';
    const CUSTOMER_ID = 'customer_id';
    const PRICELIST_ID = 'pricelist_id';
    const DISCOUNT_TYPE = 'discount_type';
    const IS_ACTIVE = 'is_active';
    const PRODUCT_DATA='product_data';
    /**
     * #@-
     */

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Set id
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id);
    
    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @param int $websiteId website id
     *
     * @return $this
     */
    public function setWebsiteId($websiteId);
    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);
    /**
     * Get Pricelist Id
     *
     * @return int|null
     */
    public function getPricelistId();

    /**
     * Set Pricelist Id
     *
     * @param int $pricelistId pricelist id
     *
     * @return $this
     */
    public function setPricelistId($pricelistId);
    
    /**
     * Get discount type
     *
     * @return int|null
     */
    public function getDiscountType();

    /**
     * Set discount type
     *
     * @param int $discountType discount type
     *
     * @return $this
     */
    public function setDiscountType($discountType);
    
    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive();
    
    /**
     * Set is active
     *
     * @param int $isActive is active
     *
     * @return $this
     */
    public function setIsActive($isActive);
    
    /**
     * Get product data
     *
     * @return \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\ProductDataInterface[]|null
     */
    public function getProductData();
    
    /**
     * Set product data
     *
     * @param \Appseconnect\B2BMage\Api\CustomerTierPrice\Data\ProductDataInterface[] $productData product data
     *
     * @return $this
     */
    public function setProductData($productData);
}
