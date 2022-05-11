<?php
/**
 * Namespace
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class BulkOrderValidator
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class BulkOrderValidator
{

    /**
     * BulkOrderValidator constructor.
     *
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory ProductFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry  StockRegistry
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
    
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Validate
     *
     * @param mixed $requestData RequestData
     *
     * @return boolean
     */
    public function validate($requestData)
    {
        $error = false;
        $productRepository = $this->productFactory->create();
        foreach ($requestData as $value) {
            $sku = $value[0];
            $qty = $value[1];
            $productId = $this->getProductIdBySku($productRepository, $sku);
            if (! $productId) {
                $error = true;
            } elseif (! $qty) {
                $error = true;
            } elseif ($qty && $productId) {
                $productStockObj = $this->stockRegistry->getStockItem($productId);
                $productModel = $this->loadProduct($productRepository, $productId);
                if ($qty > $productStockObj->getQty() && $productStockObj->getBackorders() == 0) {
                    $error = true;
                }
                if ($productModel->getStatus() == 2) {
                    $error = true;
                }
            }
        }
        return $error;
    }
    
    /**
     * LoadProduct
     *
     * @param \Magento\Catalog\Model\ProductFactory $productRepository ProductRepository
     * @param int                                   $productId         ProductId
     *
     * @return mixed
     */
    public function loadProduct($productRepository, $productId)
    {
        $productModel = $productRepository->load($productId);
        return $productModel;
    }

    /**
     * GetProductIdBySku
     *
     * @param \Magento\Catalog\Model\ProductFactory $productRepository ProductRepository
     * @param string                                $sku               Sku
     *
     * @return int
     */
    public function getProductIdBySku($productRepository, $sku)
    {
        $productId = $productRepository->getIdBySku($sku);
        return $productId;
    }
}
