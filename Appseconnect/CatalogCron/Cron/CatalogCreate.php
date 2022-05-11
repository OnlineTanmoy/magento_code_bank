<?php

namespace Appseconnect\CatalogCron\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;

class CatalogCreate
{
    /**
     * UpdateQuote constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper QuotationHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_categoryFactory = $categoryFactory;
    }

    public function execute()
    {
        $products = $this->productCollectionFactory->create()->addAttributeToSelect('*');
        $filepath = 'InSyncCommerce_Product_Catalog.csv';
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();


        $header = ['Sku', 'Name', 'Category'];
        $stream->writeCsv($header);


        foreach ($products as $product) {

            $data = [];
            $data[] = $product->getSku();
            $data[] = $product->getName();
            $data[] = $this->getCategory($product->getCategoryIds());
            $stream->writeCsv($data);
        }
    }

    public function getCategory($categoryIds)
    {
        $categoryName = '';
        foreach ($categoryIds as $categoryId) {
            $category = $this->_categoryFactory->create()->load($categoryId);
            $categoryName .= $category->getName() . ',';
        }

        return rtrim($categoryName, ',');
    }
}
