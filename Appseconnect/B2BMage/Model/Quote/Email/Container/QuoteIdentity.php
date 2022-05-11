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

namespace Appseconnect\B2BMage\Model\Quote\Email\Container;

use Magento\Store\Model\ScopeInterface;

/**
 * Class QuoteIdentity
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteIdentity extends Container implements IdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order/copy_method';

    const XML_PATH_EMAIL_COPY_TO = 'sales_email/order/copy_to';

    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';

    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/order/guest_template';

    const XML_PATH_QUOTE_EMAIL_NEW_TEMPLATE = 'insync_quotes/email/new';

    const XML_PATH_QUOTE_EMAIL_APPROVE_TEMPLATE = 'insync_quotes/email/approve';

    const XML_PATH_QUOTE_EMAIL_HOLD_TEMPLATE = 'insync_quotes/email/hold';

    const XML_PATH_QUOTE_EMAIL_UNHOLD_TEMPLATE = 'insync_quotes/email/unhold';

    const XML_PATH_QUOTE_EMAIL_CANCEL_TEMPLATE = 'insync_quotes/email/cancel';

    const XML_PATH_EMAIL_ENABLED = 'insync_quotes/general/enable_quote';

    /**
     * IsEnabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()
                ->getStoreId()
        );
    }

    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        $guestTemplateId = $this->getConfigValue(
            self::XML_PATH_EMAIL_GUEST_TEMPLATE,
            $this->getStore()->getStoreId()
        );
        return $guestTemplateId;
    }

    /**
     * Return new quote template id
     *
     * @return mixed
     */
    public function getNewTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_QUOTE_EMAIL_NEW_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return approve quote template id
     *
     * @return mixed
     */
    public function getApproveTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_QUOTE_EMAIL_APPROVE_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return hold quote template id
     *
     * @return mixed
     */
    public function getHoldTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_QUOTE_EMAIL_HOLD_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return unhold quote template id
     *
     * @return mixed
     */
    public function getUnholdTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_QUOTE_EMAIL_UNHOLD_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return cancel quote template id
     *
     * @return mixed
     */
    public function getCancelTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_QUOTE_EMAIL_CANCEL_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        $emailIdentityId = $this->getConfigValue(
            self::XML_PATH_EMAIL_IDENTITY,
            $this->getStore()->getStoreId()
        );
        return $emailIdentityId;
    }
}
