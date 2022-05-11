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
namespace Appseconnect\B2BMage\Model\Catalog;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Category
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Category extends \Magento\Catalog\Model\Category implements \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface
{
    /**
     * Set available sort by
     *
     * @param string $image Image
     *
     * @return $this
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Returns image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Retrieve count products of category
     *
     * @return int
     */
    public function getProductCount()
    {
        if (!$this->hasData(self::KEY_PRODUCT_COUNT)) {
            $count = $this->_getResource()->getProductCount($this);
            $this->setData(self::KEY_PRODUCT_COUNT, $count);
        }

        return $this->getData(self::KEY_PRODUCT_COUNT);
    }

    /**
     * Set category position
     *
     * @param int $position Position
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function setPosition($position)
    {
        return $this->setData(self::KEY_POSITION, $position);
    }

    /**
     * Set parent category ID
     *
     * @param int $parentId ParentId
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::KEY_PARENT_ID, $parentId);
    }

    /**
     * Set category name
     *
     * @param string $name Name
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * Set whether category is active
     *
     * @param bool $isActive IsActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData(self::KEY_LEVEL);
    }

    /**
     * GetChildrenData
     *
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface[]|null
     */
    public function getChildrenData()
    {
        return $this->getData(self::KEY_CHILDREN_DATA);
    }

    /**
     * Set category level
     *
     * @param int $level Level
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function setLevel($level)
    {
        return $this->setData(self::KEY_LEVEL, $level);
    }

    /**
     * GetPosition
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->getData(self::KEY_POSITION);
    }

    /**
     * Retrieve Name data wrapper
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData(self::KEY_NAME);
    }

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId()
    {
        $parentId = $this->getData(self::KEY_PARENT_ID);
        if (isset($parentId)) {
            return $parentId;
        }
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * GetIsActive
     *
     * @return                                       bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive()
    {
        return $this->getData(self::KEY_IS_ACTIVE);
    }

    /**
     * SetChildrenData
     *
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterface[] $childrenData ChildrenData
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function setChildrenData(array $childrenData = null)
    {
        return $this->setData(self::KEY_CHILDREN_DATA, $childrenData);
    }

    /**
     * Set product count
     *
     * @param int $productCount ProductCount
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function setProductCount($productCount)
    {
        return $this->setData(self::KEY_PRODUCT_COUNT, $productCount);
    }

    /**
     * Identifier setter
     *
     * @param mixed $value Value
     *
     * @return $this
     */
    public function setId($value)
    {
        parent::setId($value);
        return $this->setData('id', $value);
    }

    /**
     * Identifier getter
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_getData($this->_idFieldName);
    }
}
