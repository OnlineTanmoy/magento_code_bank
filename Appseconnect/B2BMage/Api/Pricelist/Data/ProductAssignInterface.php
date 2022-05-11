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
 * Interface ProductAssignInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ProductAssignInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const PRICELIST_ID = 'pricelist_id';

    const PRODUCT_DATA = 'product_data';

    const ERROR = 'error';

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
     * Get product data
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\ProductDataInterface[]|null
     */
    public function getProductData();

    /**
     * Set product data
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\ProductDataInterface[] $productData product data
     *
     * @return $this
     */
    public function setProductData($productData);

    /**
     * Get Error
     *
     * @return string|null
     */
    public function getError();

    /**
     * Set Error
     *
     * @param string $error error
     * 
     * @return $this
     */
    public function setError($error);
}
