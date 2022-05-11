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
 * Class UpdatePricelist
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdatePricelist extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Pricelist\Data\UpdatePricelistInterface
{
    /**
     * CustomerMetadataInterface
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    public $metadataService;

    /**
     * UpdatePricelist constructor.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory      ExtensionFactory
     * @param AttributeValueFactory                             $attributeValueFactory AttributeValueFactory
     * @param \Magento\Customer\Api\CustomerMetadataInterface   $metadataService       MetadataService
     * @param array                                             $data                  Data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        $data = []
    ) {
        $this->metadataService = $metadataService;
        parent::__construct($extensionFactory, $attributeValueFactory, $data);
    }

    /**
     * GetCustomAttributesCodes
     *
     * @return mixed
     */
    public function getCustomAttributesCodes()
    {
        if ($this->customAttributesCodes === null) {
            $this->customAttributesCodes = $this->getEavAttributesCodes($this->metadataService);
        }
        return $this->customAttributesCodes;
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
     * GetId
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }
    
    /**
     * Set website id
     *
     * @param int $websiteId WebsiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }
    
    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }
   
    /**
     * Set pricelist name
     *
     * @param string $pricelistName PricelistName
     *
     * @return $this
     */
    public function setPricelistName($pricelistName)
    {
        return $this->setData(self::PRICELIST_NAME, $pricelistName);
    }
    
    /**
     * GetPricelistName
     *
     * @return string|null
     */
    public function getPricelistName()
    {
        return $this->_get(self::PRICELIST_NAME);
    }
    
    /**
     * Set parent  id
     *
     * @param string $parentId ParentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }
    
    /**
     * GetParentId
     *
     * @return string|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Set discount factor
     *
     * @param float $discountFactor DiscountFactor
     *
     * @return $this
     */
    public function setDiscountFactor($discountFactor)
    {
        return $this->setData(self::DISCOUNT_FACTOR, $discountFactor);
    }
    
    /**
     * GetDiscountFactor
     *
     * @return float|null
     */
    public function getDiscountFactor()
    {
        return $this->_get(self::DISCOUNT_FACTOR);
    }
    
    /**
     * Set is active
     *
     * @param string $isActive IsActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
    
    /**
     * GetIsActive
     *
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }
    
    /**
     * Set Product Sku
     *
     * @param string[] $productSku ProductSku
     *
     * @return $this
     */
    public function setProductSkus(array $productSku = null)
    {
        return $this->setData(self::PRODUCT_SKUS, $productSku);
    }
    
    /**
     * GetProductSkus
     *
     * @return string[]|null
     */
    public function getProductSkus()
    {
        return $this->_get(self::PRODUCT_SKUS);
    }
}
