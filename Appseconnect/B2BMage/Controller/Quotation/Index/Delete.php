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
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

/**
 * Class Delete
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Delete extends \Appseconnect\B2BMage\Controller\Quotation\Quote
{

    /**
     * Delete quote item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (! $this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        $itemId = (int) $this->getRequest()->getParam('item_id');
        
        if ($itemId) {
            try {
                $this->customCart->removeItem($itemId)->save();
                $this->messageManager->addSuccess(__('Item has been successfully removed.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
            }
        }
        $defaultUrl = $this->urlManager->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}
