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

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

/**
 * Class AddComment
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AddComment extends \Appseconnect\B2BMage\Controller\Quotation\Quote
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
                
                $notify = true;
                
                $reviewerName = $this->getCatalogSession()->getSalesrepId()
                    ? 'Sales Rep.: ' . $this->customerFactory->create()
                    ->load($this->getCatalogSession()->getSalesrepId())
                    ->getName() : $quote->getContactName();
                
                $commentProvider = $reviewerName;
                
                $history = $quote->addStatusHistoryComment($data['comment'], $quote->getStatus());
                $history->setName($reviewerName);
                $history->setIsVisibleOnFront(true);
                $history->setIsCustomerNotified($notify);
                $history->save();
                
                $comment = trim(strip_tags($data['comment']));
                
                $quote->save();
                $action = 'comment';
                
                $this->commentSender->send($quote, $action, $commentProvider, $notify, $comment);
                
                $result = $this->resultFactory
                    ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                    ->setData(
                        [
                        'status' => "201"
                         ]
                    );
                
                return $result;
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
                $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData(
            [
            'status' => "400"
            ]
        );
    }
}
