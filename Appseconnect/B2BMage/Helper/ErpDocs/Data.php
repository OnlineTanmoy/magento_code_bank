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
namespace Appseconnect\B2BMage\Helper\ErpDocs;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * ErpInvoiceFactory
     *
     * @var \Appseconnect\B2BMage\Model\ErpInvoiceFactory
     */
    public $erpInvoiceFactory;

    /**
     * Data constructor.
     *
     * @param \Appseconnect\B2BMage\Model\ErpInvoiceFactory $erpInvoiceFactory ErpInvoiceFactory
     */
    public function __construct(
        \Appseconnect\B2BMage\Model\ErpInvoiceFactory $erpInvoiceFactory
    ) {
    
        $this->erpInvoiceFactory = $erpInvoiceFactory;
    }

    /**
     * GetPdfData
     *
     * @param string $invoiceId InvoiceId
     *
     * @return NULL|array
     */
    public function getPdfData($invoiceId)
    {
        $pdfCollection = $this->erpInvoiceFactory->create()
            ->getCollection()
            ->addFieldToFilter('invoice_increment_id', $invoiceId);
        $data = $pdfCollection->getData();
        $data = isset($data[0]) ? $data[0] : null;
        
        return $data;
    }
}
