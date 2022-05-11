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
use Magento\Framework\Api\AbstractExtensibleObject;
use Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterface;

/**
 * Class ProductAssign
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductAssign extends AbstractExtensibleObject implements ProductAssignInterface
{
    /**
     * Get Pricelist Id
     *
     * @return int|null
     */
    public function getPricelistId()
    {
        return $this->_get(self::PRICELIST_ID);
    }
    
    /**
     * Get Error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }

    /**
     * Get product data
     *
     * @return \Appseconnect\B2BMage\Api\Pricelist\Data\ProductDataInterface[]|null
     */
    public function getProductData()
    {
        return $this->_get(self::PRODUCT_DATA);
    }

    /**
     * Set Pricelist Id
     *
     * @param int $pricelistId PricelistId
     *
     * @return $this
     */
    public function setPricelistId($pricelistId)
    {
        return $this->setData(self::PRICELIST_ID, $pricelistId);
    }
    
    /**
     * Set Error
     *
     * @param string $error Error
     *
     * @return $this
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * Set product data
     *
     * @param \Appseconnect\B2BMage\Api\Pricelist\Data\ProductDataInterface[] $productData ProductData
     *
     * @return $this
     */
    public function setProductData($productData)
    {
        return $this->setData(self::PRODUCT_DATA, $productData);
    }
}
