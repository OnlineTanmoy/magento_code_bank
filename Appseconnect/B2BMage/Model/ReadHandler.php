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
namespace Appseconnect\B2BMage\Model;

/**
 * Class ReadHandler
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ReadHandler implements \Magento\Framework\EntityManager\Operation\ExtensionInterface
{
    /**
     * Stock registry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * ReadHandler constructor.
     *
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry stock registry
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {

        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Execute
     *
     * @param $product   product
     * @param array $arguments argument
     *
     * @return mixed
     */
    public function execute($product, $arguments = [])
    {
        if ($product->getExtensionAttributes()->getStockItem() !== null) {
            return $product;
        }

        $stockItem =$this->stockRegistry->getStockItem($product->getId());
        $extensionAttributes = $product->getExtensionAttributes();
        $extensionAttributes->setStockItem($stockItem);
        $product->setExtensionAttributes($extensionAttributes);

        return $product;
    }
}
