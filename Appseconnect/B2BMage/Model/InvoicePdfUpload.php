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

use Appseconnect\B2BMage\Api\ErpDocs\InvoicePdfUploadInterface;
use Appseconnect\B2BMage\Api\ErpDocs\Data\PdfInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Mobiletheme
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class InvoicePdfUpload implements InvoicePdfUploadInterface
{
    /**
     * File system
     *
     * @var Filesystem
     */
    public $fileSystem;
    
    /**
     * Io adapter
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $ioAdapter;
    
    /**
     * Erp invoice
     *
     * @var ErpInvoice
     */
    public $erpInvoice;

    /**
     * Resource
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;
    
    /**
     * Invoice
     *
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    public $invoice;

    /**
     * InvoicePdfUpload constructor.
     *
     * @param Filesystem                                $fileSystem filesystem
     * @param Filesystem\Io\File                        $ioAdapter  ioadapter
     * @param ErpInvoice                                $erpInvoice erpinvoice
     * @param \Magento\Framework\App\ResourceConnection $resources  resource
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoice    invoice
     */
    public function __construct(
        Filesystem $fileSystem,
        \Magento\Framework\Filesystem\Io\File $ioAdapter,
        ErpInvoice $erpInvoice,
        \Magento\Framework\App\ResourceConnection $resources,
        \Magento\Sales\Model\Order\InvoiceFactory $invoice
    ) {
            
            $this->fileSystem = $fileSystem;
            $this->ioAdapter = $ioAdapter;
            $this->erpInvoice = $erpInvoice;
            $this->resources = $resources;
            $this->invoice = $invoice;
    }

    /**
     * Uploader
     *
     * @param PdfInterface $pdf pdf
     *
     * @return PdfInterface
     */
    public function upload(PdfInterface $pdf)
    {
        if ($pdf->getPdfData()) {
            $invoiceData = $this->invoice->create()->loadByIncrementId($pdf->getInvoiceIncrementId());
            if ($invoiceData->getOrderId() != $pdf->getOrderId()) {
                throw new CouldNotSaveException(__("Could not upload the invoice."));
            }
            $destinationPath = $this->getDestinationPath();
            $destinationPath = $destinationPath . 'media/insync/invoice/';
            // Decode pdf content
            $pdfDecoded = base64_decode($pdf->getPdfData());
            // Write data back to pdf file
            $this->ioAdapter->checkAndCreateFolder($destinationPath);
            $fileName = 'sapInvoice' . $pdf->getInvoiceIncrementId() . date('YmdHis') . '.pdf';
            $args['path']='pub/media/insync/invoice';
            $pdfUpload = $this->ioAdapter->open($args, 'w');
            $this->ioAdapter->write($fileName, (string)$pdfDecoded);
            // close output file
            $this->ioAdapter->close($pdfUpload);
            $pdf->setPdfData($fileName);
            $connection = $this->resources->getConnection();
            $where = [];
            $where['invoice_increment_id=?'] = $pdf->getInvoiceIncrementId();
            $connection->delete($this->resources->getTableName('insync_erp_invoice'), $where);
            $pdfData = [];
            $pdfData['order_id'] = $pdf->getOrderId();
            $pdfData['invoice_increment_id'] = $pdf->getInvoiceIncrementId();
            $pdfData['pdf_path'] = '/insync/invoice/' . $fileName;
            $pdfInstance = $this->erpInvoice->setData($pdfData);
            $pdfInstance->save();
        }
        return $pdf;
    }

    /**
     * Get destination path
     *
     * @return mixed
     */
    public function getDestinationPath()
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::PUB)->getAbsolutePath('/');
    }
}
