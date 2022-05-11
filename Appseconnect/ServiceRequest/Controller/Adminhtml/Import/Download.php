<?php

namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Download
 * To implement the custom table download process from admin
 * @package Appseconnect\CustomImport\Controller\Adminhtml\Import
 */
class Download extends \Magento\ImportExport\Controller\Adminhtml\Import\Download
{
    /**
     * @var \Appseconnect\SapItemGroupDiscount\Model\SapItemPropertyFactory
     */
    protected $sapItemPropertyFactory;

    /**
     * Download constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\ImportExport\Model\Import\SampleFileProvider|null $sampleFileProvider
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider = null
    )
    {
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct($context, $fileFactory, $resultRawFactory, $readFactory, $componentRegistrar, $sampleFileProvider);
    }

    /**
     * Override default sapmle download file for custom table download from admin
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (!in_array($data['filename'], array('appseconnect_product_serial_import'))) {
            // let's parent handle the remaing service for downloading sample CSV service
            return parent::execute();
        }

        $csvfilename = 'sample.csv';
        $name = date('m_d_Y_H_i_s');
        $filepath = 'export/custom' . $name . '.csv';
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        if ($data['filename'] == 'appseconnect_product_serial_import') {
            // just to send some dummy data
            $csvfilename = 'appseconnect_product_serial_import.csv';
            $stream->writeCsv(array("sku", "serial_no", "warranty_months", "is_active"));
            $stream->writeCsv(array("TEST-Product", "TESTPROD765765", "12", "1"));
        }

        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }
}
