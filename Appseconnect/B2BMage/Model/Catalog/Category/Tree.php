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
namespace Appseconnect\B2BMage\Model\Catalog\Category;

/**
 * Class Tree
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Tree extends \Magento\Catalog\Model\Category\Tree
{
    /**
     * CustomTreeInterfaceFactory
     *
     * @var \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterfaceFactory
     */
    public $customTreeFactory;

    /**
     * CategoryFactory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * Tree constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree                $categoryTree        CategoryTree
     * @param \Magento\Store\Model\StoreManagerInterface                        $storeManager        StoreManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection          $categoryCollection  CategoryCollection
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory            $treeFactory         TreeFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\TreeFactory|null    $treeResourceFactory TreeResourceFactory
     * @param \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterfaceFactory $customTreeFactory   CustomTreeFactory
     * @param \Magento\Catalog\Model\CategoryFactory                            $categoryFactory     CategoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory $treeFactory,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $treeResourceFactory = null,
        \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterfaceFactory $customTreeFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct(
            $categoryTree,
            $storeManager,
            $categoryCollection,
            $treeFactory,
            $treeResourceFactory
        );
        $this->customTreeFactory = $customTreeFactory;
        $this->categoryFactory = $categoryFactory;

    }
    /**
     * Get tree by node.
     *
     * @param \Magento\Framework\Data\Tree\Node $node         Node
     * @param int                               $depth        Depth
     * @param int                               $currentLevel CurrentLevel
     *
     * @return \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface
     */
    public function getTree($node, $depth = null, $currentLevel = 0)
    {
        $children = $this->getChildren($node, $depth, $currentLevel);
        $category = $this->categoryFactory->create()->load($node->getId());
        $tree = $this->customTreeFactory->create();
        $tree->setId($node->getId())
            ->setParentId($node->getParentId())
            ->setName($category->getName())
            ->setPosition($category->getPosition())
            ->setLevel($node->getLevel())
            ->setIsActive($category->getIsActive())
            ->setProductCount($category->getProductCount())
            ->setChildrenData($children)
            ->setImage($category->getImageUrl());

        return $tree;
    }
}
