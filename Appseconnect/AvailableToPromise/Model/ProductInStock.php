<?php

namespace Appseconnect\AvailableToPromise\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Appseconnect\AvailableToPromise\Api\ProductInStock\Data\ProductInStockInterface;

class ProductInStock extends \Magento\Framework\Model\AbstractModel implements ProductInStockInterface
{
    public function getId()
    {
        return $this->getData(ProductInStockInterface::ID);
    }

    public function getAvailableDate()
    {
        return $this->getData(ProductInStockInterface::AVAILABLE_DATE);
    }

    public function getProductSku()
    {
        return $this->getData(ProductInStockInterface::PRODUCT_SKU);
    }

    public function getQuantity()
    {
        return $this->getData(ProductInStockInterface::QUANTITY);
    }

    public function getAvailableQuantity()
    {
        return $this->getData(ProductInStockInterface::AVAILABLE_QUANTITY);
    }

    public function getDocumentType()
    {
        return $this->getData(ProductInStockInterface::DOCUMENT_TYPE);
    }

    public function getWarehouse()
    {
        return $this->getData(ProductInStockInterface::WAREHOUSE);
    }

    public function getSyncFlag()
    {
        return $this->getData(ProductInStockInterface::SYNC_FLAG);
    }

    public function getPostingDate()
    {
        return $this->getData(ProductInStockInterface::POSTING_DATE);
    }

    public function setId($id)
    {
        return $this->setData(ProductInStockInterface::ID, $id);
    }

    public function setAvailableDate($availableDate)
    {
        return $this->setData(ProductInStockInterface::AVAILABLE_DATE, $availableDate);
    }

    public function setProductSku($productSku)
    {
        return $this->setData(ProductInStockInterface::PRODUCT_SKU, $productSku);
    }

    public function setQuantity($quantity)
    {
        return $this->setData(ProductInStockInterface::QUANTITY, $quantity);
    }

    public function setAvailableQuantity($availableQuantity)
    {
        return $this->setData(ProductInStockInterface::AVAILABLE_QUANTITY, $availableQuantity);
    }

    public function setDocumentType($documentType)
    {
        return $this->setData(ProductInStockInterface::DOCUMENT_TYPE, $documentType);
    }

    public function setWarehouse($warehouse)
    {
        return $this->setData(ProductInStockInterface::WAREHOUSE, $warehouse);
    }

    public function setSyncFlag($syncFlag)
    {
        return $this->setData(ProductInStockInterface::SYNC_FLAG, $syncFlag);
    }

    public function setPostingDate($postingDate)
    {
        return $this->setData(ProductInStockInterface::POSTING_DATE, $postingDate);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appseconnect\AvailableToPromise\Model\ResourceModel\ProductInStock');
    }
}