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

use Magento\Store\Model\Store;

/**
 * Interface IdentityInterface
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface IdentityInterface
{

    /**
     * Bool
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Mixed data
     *
     * @return mixed
     */
    public function getGuestTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getNewTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getApproveTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getHoldTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getUnholdTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getCancelTemplateId();

    /**
     * Mixed Data
     *
     * @return mixed
     */
    public function getEmailIdentity();

    /**
     * String
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * String
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Store
     *
     * @return Store
     */
    public function getStore();

    /**
     * Store
     *
     * @param Store $store Store
     *
     * @return mixed
     */
    public function setStore(Store $store);

    /**
     * SetCustomerEmail
     *
     * @param string $email Email
     *
     * @return mixed
     */
    public function setCustomerEmail($email);

    /**
     * SetCustomerName
     *
     * @param string $name Name
     *
     * @return mixed
     */
    public function setCustomerName($name);
}
