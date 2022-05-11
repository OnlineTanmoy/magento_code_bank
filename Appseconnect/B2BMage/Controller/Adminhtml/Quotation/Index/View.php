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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Index;

use Magento\Backend\App\Action;

/**
 * Class View
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class View extends \Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Quote
{

    /**
     * View quote detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $quote = $this->initQuote();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($quote) {
            try {
                $resultPage = $this->initAction();
                $resultPage->getConfig()
                    ->getTitle()
                    ->prepend(__('Quotes'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addError(__('Exception occurred during quote load'));
                $resultRedirect->setPath('/');
                return $resultRedirect;
            }
            $resultPage->getConfig()
                ->getTitle()
                ->prepend(sprintf("#%s", $quote->getId()));
            return $resultPage;
        }
        $resultRedirect->setPath('quotation');
        return $resultRedirect;
    }
}
