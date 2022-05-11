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

namespace Appseconnect\B2BMage\Model\Quote;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class ChangeQuoteControl
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ChangeQuoteControl extends \Magento\Quote\Model\ChangeQuoteControl
{

    /**
     * UserContext
     *
     * @var UserContextInterface $userContext
     */
    public $userContext;

    /**
     * ContactPerson
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson
     */
    public $helperContactPerson;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helperSalerep;

    /**
     * ChangeQuoteControl constructor.
     *
     * @param UserContextInterface                            $userContext         UserContext
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data      $helperSalerep       HelperSalerep
     */
    public function __construct(
        UserContextInterface $userContext,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helperSalerep
    ) {
        parent::__construct($userContext);
        $this->userContext = $userContext;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperSalerep = $helperSalerep;
    }

    /**
     * IsAllowed
     *
     * @param CartInterface $quote Quote
     *
     * @return bool
     */
    public function isAllowed(CartInterface $quote): bool
    {
        switch ($this->userContext->getUserType()) {
        case UserContextInterface::USER_TYPE_CUSTOMER:
            if ($quote->getCustomerId() != $this->userContext->getUserId()) {
                $company = $this->helperContactPerson->getCustomerId($quote->getCustomerId());
                $salesrep = $this->helperSalerep->getSalesrepId($company['customer_id']);

                $salesrepId = $this->helperSalerep->getSalesrepCustomerId($salesrep[0]['salesrep_id']);
                if ($this->userContext->getUserId() == $salesrepId) {
                    $isAllowed = true;
                }
            } else {
                $isAllowed = true;
            }
            break;
        case UserContextInterface::USER_TYPE_GUEST:
            $isAllowed = ($quote->getCustomerId() === null);
            break;
        case UserContextInterface::USER_TYPE_ADMIN:
        case UserContextInterface::USER_TYPE_INTEGRATION:
            $isAllowed = true;
            break;
        default:
            $isAllowed = false;
        }

        return $isAllowed;
    }
}
