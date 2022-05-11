<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Plugin\Sales\Controller\AbstractController;

use Magento\Customer\Model\Session;

/**
 * Class OrderLoaderPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderViewAuthorizationPlugin
{

    /**
     * Session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Config
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    public $orderConfig;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Class variable Initialize
     *
     * @param \Magento\Customer\Model\Session                 $customerSession     CustomerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Sales\Model\Order\Config               $orderConfig         OrderConfig
     */
    public function __construct(
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->orderConfig = $orderConfig;
        $this->divisionHelper = $divisionHelper;
    }

    /**
     * AroundCanView
     *
     * @param \Magento\Sales\Controller\AbstractController\OrderViewAuthorization $subject Subject
     * @param \Closure                                                            $proceed Proceed
     * @param \Magento\Sales\Model\Order                                          $order   Order
     *
     * @return boolean
     */
    public function aroundCanView(
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorization $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order $order
    ) {
        $parentCustomerId = null;
        $isAllowed = false;
        $customerId = $this->customerSession->getCustomerId();
        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
        $customer = $this->customerSession->getCustomer();

        $isAdministrator = $customer->getContactpersonRole() == 1 ? true : false;
        $customerType = $customer->getCustomerType();

        if ($customerType == 3) {
            if ($isAdministrator) {
                if ($this->divisionHelper->isParentContact($customerId)) {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $divisions = $this->divisionHelper->getChildAllDivision($parentCustomerMapData['customer_id']);
                    $contactId[] = $parentCustomerMapData['customer_id'];
                    foreach($divisions as $divisionall) {
                        if(isset($divisionall['division_id']) && $divisionall['division_id'] != '') {
                            $contactId[] = $divisionall['division_id'];
                        }
                    }

                    if(in_array($order->getCustomerId(), $contactId)) {
                        $isAllowed = true;
                    }
                } else {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $parentCustomerId = $parentCustomerMapData['customer_id'];
                    if ($order->getCustomerId() == $parentCustomerId) {
                        $isAllowed = true;
                    }
                }
            } elseif ($order->getContactPersonId() == $customerId) {
                $isAllowed = true;
            }
        } elseif ($customerType == 2) {
            if ($order->getSalesrepId() == $customerId) {
                $isAllowed = true;
            }
        }

        if ($order->getId()
            && $order->getCustomerId()
            && $isAllowed
            && in_array($order->getStatus(), $availableStatuses, true)
        ) {
            return true;
        }

        if ($order->getId()
            && $order->getCustomerId()
            && $order->getCustomerId() == $customerId
            && in_array($order->getStatus(), $availableStatuses, true)
        ) {
            return true;
        }
        return false;
    }
}
