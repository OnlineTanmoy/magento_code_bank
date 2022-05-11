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

namespace Appseconnect\B2BMage\Model\Sales\ResourceModel\Order\Plugin;

use Magento\Authorization\Model\UserContextInterface;

/**
 * Class Authorization
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Authorization extends \Magento\Sales\Model\ResourceModel\Order\Plugin\Authorization
{
    /**
     * Usercontext
     *
     * @var UserContextInterface $userContext
     */
    protected $userContext;

    /**
     * Contactperson helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson
     */
    public $helperContactPerson;

    /**
     * Salesrep helper
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helperSalerep;

    /**
     * Authorization constructor.
     *
     * @param UserContextInterface                            $userContext         usercontext
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contactperson helper
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data      $helperSalerep       salesrep helper
     */
    public function __construct(UserContextInterface $userContext,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helperSalerep
    ) {
        parent::__construct($userContext);
        $this->userContext = $userContext;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperSalerep = $helperSalerep;
    }

    /**
     * IsAllow
     *
     * @param \Magento\Sales\Model\Order $order order
     *
     * @return bool
     */
    protected function isAllowed(\Magento\Sales\Model\Order $order)
    {

        if ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
            return true;
        } else if ($order->getCustomerId() != $this->userContext->getUserId()) {
            $isAllowed = false;

            $salesrep = $this->helperSalerep->getSalesrepId($order->getCustomerId());

            $contactPersons = $this->helperContactPerson->getContactPersonId($order->getCustomerId());

            $contactArray = array();
            foreach ($contactPersons as $contactPerson) {
                $contactArray[] = $contactPerson['contactperson_id'];
            }

            if (in_array($this->userContext->getUserId(), $contactArray)) {
                $isAllowed = true;
            } else if (!empty($salesrep)) {
                $salesrepId = $this->helperSalerep->getSalesrepCustomerId($salesrep[0]['salesrep_id']);

                if ($this->userContext->getUserId() == $salesrepId) {
                    $isAllowed = true;
                }
            } else {
                $isAllowed = true;
            }
            return $isAllowed;
        } else {
            return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
                ? $order->getCustomerId() == $this->userContext->getUserId()
                : true;
        }
    }
}
