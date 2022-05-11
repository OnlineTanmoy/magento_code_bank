<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\CreditLimit\Data;

/**
 * Interface CreditLimitInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CreditLimitInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**
     * #@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_ID = 'customer_id';

    const CREDIT_LIMIT = 'credit_limit';

    const AVAILABLE_BALANCE = 'available_balance';

    const INCREMENT_ID = 'increment_id';

    const DEBIT_AMOUNT = 'debit_amount';

    const CREDIT_AMOUNT = 'credit_amount';

    /**
     * To set credit amount
     *
     * @return float|null
     */
    public function getCreditAmount();

    /**
     * To set credit amount
     *
     * @param float $creditAmount credit amount
     *
     * @return $this
     */
    public function setCreditAmount($creditAmount);

    /**
     * To set debit amount
     *
     * @return float|null
     */
    public function getDebitAmount();

    /**
     * To set debit amount
     *
     * @param float $debitAmount debit amount
     *
     * @return $this
     */
    public function setDebitAmount($debitAmount);

    /**
     * Order Increment Id
     *
     * @return string|null
     */
    public function getIncrementId();

    /**
     * Order Increment Id
     *
     * @param string $incrementId increment id
     *
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * Get Customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer id
     *
     * @param int $customerId customer id
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Credit Limit
     *
     * @return float|null
     */
    public function getCreditLimit();

    /**
     * Set Credit Limit
     *
     * @param float $creditLimit credit limit
     *
     * @return $this
     */
    public function setCreditLimit($creditLimit);

    /**
     * Get Available Balance
     *
     * @return float|null
     */
    public function getAvailableBalance();

    /**
     * Set Available Balance
     *
     * @param float $availableBalance available balance
     *
     * @return $this
     */
    public function setAvailableBalance($availableBalance);
}
