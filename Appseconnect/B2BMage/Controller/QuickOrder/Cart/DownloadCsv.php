<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\QuickOrder\Cart;

use Magento\Sales\Controller\OrderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class DownloadCsv
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DownloadCsv extends \Magento\Framework\App\Action\Action
{
    /**
     * Product collection factory
     */
    protected $productCollectionFactory;

    /**
     * Download csv constructor
     *
     * @param Context                                                        $context                  context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory product collection
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Download csv
     *
     * @return void
     */
    public function execute()
    {
        $notificationId = $this->getRequest()->getParam('notification_id');
        $heading = [
            __('Sku'),
            __('Qty')
        ];
        $outputFile = "Quickorder". date('Ymd_His').".csv";
        $handle = fopen($outputFile, 'w');
        fputcsv($handle, $heading);

        $row = [
            'INS4521',
            15,
        ];
        fputcsv($handle, $row);

        $this->downloadCsv($outputFile);
    }

    /**
     * Download csv
     *
     * @param string $file file
     *
     * @return void
     */
    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            //set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();flush();
            readfile($file);
        }
    }
}
