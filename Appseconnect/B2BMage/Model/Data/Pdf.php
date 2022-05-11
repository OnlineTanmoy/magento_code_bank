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
 * Class Pdf
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Pdf extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Appseconnect\B2BMage\Api\ErpDocs\Data\PdfInterface
{

    /**
     * Get order Id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }
    
    /**
     * Set order Id
     *
     * @param int $orderId OrderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get invoice
     *
     * @return string|null
     */
    public function getInvoiceIncrementId()
    {
        return $this->_get(self::INVOICE);
    }
    
    /**
     * Set invoice
     *
     * @param string $invoice Invoice
     *
     * @return $this
     */
    public function setInvoiceIncrementId($invoice)
    {
        return $this->setData(self::INVOICE, $invoice);
    }

    /**
     * Get Pdf data
     *
     * @return string|null
     */
    public function getPdfData()
    {
        return $this->_get(self::PDF_DATA);
    }
    
    /**
     * Set Pdf data
     *
     * @param string $data Data
     *
     * @return $this
     */
    public function setPdfData($data)
    {
        return $this->setData(self::PDF_DATA, $data);
    }
}
