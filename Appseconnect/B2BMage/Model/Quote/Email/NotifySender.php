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
namespace Appseconnect\B2BMage\Model\Quote\Email;

use Appseconnect\B2BMage\Model\Quote;

/**
 * Abstract Class NotifySender
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class NotifySender extends Sender
{

    /**
     * Send email to customer
     *
     * @param Quote  $quote  Quote
     * @param string $action Action
     * @param bool   $notify Notify
     *
     * @return bool
     */
    public function checkAndSend(Quote $quote, $action = null, $notify = true)
    {
        $this->identityContainer->setStore($quote->getStore());
        if (! $this->identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($quote, $action);
        
        $sender = $this->getSender();
        
        if ($notify) {
            $sender->send();
        }
        
        return true;
    }
}
