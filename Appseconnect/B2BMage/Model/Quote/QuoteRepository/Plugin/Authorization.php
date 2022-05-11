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

namespace Appseconnect\B2BMage\Model\Quote\QuoteRepository\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Authorization
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Authorization extends \Magento\Quote\Model\QuoteRepository\Plugin\Authorization
{

    /**
     * UserContext
     *
     * @var UserContextInterface $userContext
     */
    protected $userContext;

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
     * Authorization constructor.
     *
     * @param UserContextInterface                            $userContext         UserContext
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data      $helperSalerep       HelperSalerep
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
     * IsAllowed
     *
     * @param \Magento\Quote\Model\Quote $quote Quote
     *
     * @return bool
     */
    protected function isAllowed(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->getCustomerId() != $this->userContext->getUserId()) {
            $isAllowed = false;
            $company = $this->helperContactPerson->getCustomerId($quote->getCustomerId());
            $salesrep = $this->helperSalerep->getSalesrepId($company['customer_id']);

            $salesrepId = $this->helperSalerep->getSalesrepCustomerId($salesrep[0]['salesrep_id']);
            if ($this->userContext->getUserId() == $salesrepId) {
                $isAllowed = true;
            }
            return $isAllowed;
        } else {
            return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
                ? $quote->getCustomerId() === null || $quote->getCustomerId() == $this->userContext->getUserId()
                : true;
        }
    }
}
