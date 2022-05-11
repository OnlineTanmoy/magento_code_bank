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
namespace Appseconnect\B2BMage\Api\Catalog\Data;

/**
 * Interface CustomTreeInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CustomTreeInterface
{
    const IMAGE = 'category_image';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Id
     *
     * @param int $id set id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get parent category ID
     *
     * @return int
     */
    public function getParentId();

    /**
     * Set parent category ID
     *
     * @param int $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get category name
     *
     * @return string
     */
    public function getName();

    /**
     * Set category name
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Check whether category is active
     *
     * @return                                       bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive();

    /**
     * Set whether category is active
     *
     * @param bool $isActive active flag
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get category position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set category position
     *
     * @param int $position position
     *
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get category level
     *
     * @return int
     */
    public function getLevel();

    /**
     * Set category level
     *
     * @param int $level level
     *
     * @return $this
     */
    public function setLevel($level);

    /**
     * Get product count
     *
     * @return int
     */
    public function getProductCount();

    /**
     * Set product count
     *
     * @param int $productCount product count
     *
     * @return $this
     */
    public function setProductCount($productCount);

    /**
     * Get Child Data
     *
     * @return \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface[]
     */
    public function getChildrenData();

    /**
     * Set child data
     * 
     * @param \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface[] $childrenData child data
     *
     * @return $this
     */
    public function setChildrenData(array $childrenData = null);

    /**
     * Get category image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set category image
     *
     * @param string $image images path
     *
     * @return $this
     */
    public function setImage($image);
}
