<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Plugin\Quote\Model;

/**
 * Class QuoteRepository
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteRepository
{
    /**
     * ProductRepositoryInterfaceFactory
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterfaceFactory
     */
    public $productRepositoryFactory;

    /**
     * Image
     *
     * @var \Magento\Catalog\Helper\Image
     */
    public $helperImage;

    /**
     * QuoteRepository constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory ProductRepositoryFactory
     * @param \Magento\Catalog\Helper\Image                          $helperImage              HelperImage
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Catalog\Helper\Image $helperImage
    ) {
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->helperImage = $helperImage;
    }

    /**
     * AfterGet
     *
     * @param \Magento\Quote\Model\QuoteRepository $subject Subject
     * @param $quote   Quote
     *
     * @return mixed
     */
    public function afterGet(\Magento\Quote\Model\QuoteRepository $subject, $quote)
    {
        if ($quote->getItems()) {
            foreach ($quote->getItems() as $itemListVal) {
                $extensionAttributes = $itemListVal->getExtensionAttributes();

                if ($itemListVal->getProductId()) {
                    $product = $this->productRepositoryFactory->create()->getById($itemListVal->getProductId());
                    $imageUrl = $this->helperImage->init($product, 'product_page_image_small')
                        ->setImageFile($product->getSmallImage())// image,small_image,thumbnail
                        ->getUrl();
                    $extensionAttributes->setImage($imageUrl);
                    $itemListVal->setExtensionAttributes($extensionAttributes);
                }
            }
        }

        return $quote;
    }
}
