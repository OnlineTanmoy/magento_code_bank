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
 * Class AddComment
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AddComment extends \Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Quote
{

    /**
     * Add order comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quote = $this->initQuote();
        
        if ($quote) {
            try {
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a comment.'));
                }
                
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;
                
                $commentProvider = 'Admin';
                
                $history = $quote->addStatusHistoryComment($data['comment'], $quote->getStatus());
                $history->setName('Admin');
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->save();
                
                $comment = trim(strip_tags($data['comment']));
                
                $quote->save();
                $action = 'comment';
                
                $this->quoteCommentSender->send($quote, $action, $commentProvider, $notify, $comment);
                
                return $this->resultPageFactory->create();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $response = [
                    'error' => true,
                    'message' => __('We cannot add quote history.')
                ];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('b2bmage/quotation/index_index');
    }
}
