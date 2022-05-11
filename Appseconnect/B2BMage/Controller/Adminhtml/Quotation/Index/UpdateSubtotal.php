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
use Appseconnect\B2BMage\Model\Quote\Email\Sender\QuoteCommentSender;

/**
 * Class UpdateSubtotal
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdateSubtotal extends \Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Quote
{

    /**
     * Add order comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quote = $this->initQuote();
        return $this->resultPageFactory->create();
    }
}
