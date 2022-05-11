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
namespace Appseconnect\B2BMage\Api\ErpDocs\Data;

/**
 * Interface PdfInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface PdfInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const PDF_DATA = 'pdf_data';

    const INVOICE = 'invoice_increment_id';

    const ORDER_ID = 'order_id';

    /**
     * Get order Id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order Id
     *
     * @param int $orderId order id
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get invoice
     *
     * @return string|null
     */
    public function getInvoiceIncrementId();

    /**
     * Set invoice
     *
     * @param string $invoice invoice
     *
     * @return $this
     */
    public function setInvoiceIncrementId($invoice);

    /**
     * Get Base64 data of pdf
     *
     * @return string|null
     */
    public function getPdfData();

    /**
     * Set Base64 data of pdf
     *
     * @param string $pdfData pdf data
     *
     * @return $this
     */
    public function setPdfData($pdfData);
}
