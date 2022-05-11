<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\Sales\Configurable;

use Magento\Catalog\Model\Product;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data
{

    /**
     * Catalog Image Helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    public $imageHelper;

    /**
     * StockRegistryInterface
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * Data constructor.
     *
     * @param \Magento\Catalog\Helper\Image                        $imageHelper   ImageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry StockRegistry
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Retrieve collection of gallery images
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product Product
     *
     * @return \Magento\Catalog\Model\Product\Image[]|null
     */
    public function getGalleryImages(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $images = $product->getMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
            foreach ($images as $image) {
                $image->setData(
                    'small_image_url', $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'medium_image_url', $this->imageHelper->init($product, 'product_page_image_medium')
                        ->constrainOnly(true)
                        ->keepAspectRatio(true)
                        ->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'large_image_url', $this->imageHelper->init($product, 'product_page_image_large')
                        ->constrainOnly(true)
                        ->keepAspectRatio(true)
                        ->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
            }
        }
        
        return $images;
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct  CurrentProduct
     * @param array                          $allowedProducts AllowedProducts
     *
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $images = $this->getGalleryImages($product);
            if ($images) {
                foreach ($images as $image) {
                    $options['images'][$productId][] = [
                        'thumb' => $image->getData('small_image_url'),
                        'img' => $image->getData('medium_image_url'),
                        'full' => $image->getData('large_image_url'),
                        'caption' => $image->getLabel(),
                        'position' => $image->getPosition(),
                        'isMain' => $image->getFile() == $product->getImage()
                    ];
                }
            }
            $quantityData = $this->stockRegistry->getStockItem($product->getId());
            $quantity = $quantityData['qty'];
            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                
                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
                $options['index'][$productId]['sku'] = $product->getSku();
                $options['index'][$productId]['qty'] = $quantity;
            }
        }
        return $options;
    }

    /**
     * Get allowed attributes
     *
     * @param \Magento\Catalog\Model\Product $product Product
     *
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }
}
