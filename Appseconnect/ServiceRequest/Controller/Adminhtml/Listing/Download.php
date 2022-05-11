<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing;

use Magento\Framework\App\Action\Context;

class Download extends \Magento\Backend\App\Action
{

    /**
     * @var Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_downloader;

    /**
     * @var Magento\Framework\Filesystem\DirectoryList
     */
    protected $_directory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $directory
    ) {
        $this->_downloader =  $fileFactory;
        $this->directory = $directory;
        parent::__construct($context);
    }

    public function execute()
    {
        $fileName = $_REQUEST['filename'];
        $file = $this->directory->getPath("media")."/connote/".$fileName;
        // do your validations

        /**
         * do file download
         */
        return $this->_downloader->create(
            $fileName,
            @file_get_contents($file)
        );
    }


}
