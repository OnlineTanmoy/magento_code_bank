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
namespace Appseconnect\B2BMage\Api\ErpDocs;

/**
 * Interface InvoicePdfUploadInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface InvoicePdfUploadInterface
{
    /**
     * Upload invoice
     *
     * @param \Appseconnect\B2BMage\Api\ErpDocs\Data\PdfInterface $pdf pdf
     * 
     * @return \Appseconnect\B2BMage\Api\ErpDocs\Data\PdfInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function upload(\Appseconnect\B2BMage\Api\ErpDocs\Data\PdfInterface $pdf);
}
