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

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Pricelist
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Pricelist extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\Pricelist\Data\PricelistInterface
{
    /**
     * CustomerMetadataInterface
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    public $metadataService;

    /**
     * Initialize dependencies.
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
     * Get WebsiteId
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
     * Set factor
     *
     * @param float $factor Factor
     *
     * @return $this
     */
    public function setFactor($factor)
    {
        return $this->setData(self::FACTOR, $factor);
    }
    
    /**
     * Get factor
     *
     * @return float|null
     */
    public function getFactor()
    {
        return $this->_get(self::FACTOR);
    }
    
    /**
     * Set is active
     *
     * @param int $isActive IsActive
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
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }
}
