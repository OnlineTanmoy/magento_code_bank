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
namespace Appseconnect\B2BMage\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Appseconnect\B2BMage\Api\CreditLimit\Data\CreditLimitInterface;

/**
 * Class CreditLimit
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CreditLimit extends AbstractExtensibleObject implements CreditLimitInterface
{

    /**
     * To set credit amount
     *
     * @return float|null
     */
    public function getCreditAmount()
    {
        return $this->_get(self::CREDIT_AMOUNT);
    }

    /**
     * To set credit amount
     *
     * @param float $creditAmount CreditAmount
     *
     * @return $this
     */
    public function setCreditAmount($creditAmount)
    {
        return $this->setData(self::CREDIT_AMOUNT, $creditAmount);
    }

    /**
     * To set debit amount
     *
     * @return float|null
     */
    public function getDebitAmount()
    {
        return $this->_get(self::DEBIT_AMOUNT);
    }

    /**
     * To set debit amount
     *
     * @param float $debitAmount DebitAmount
     *
     * @return $this
     */
    public function setDebitAmount($debitAmount)
    {
        return $this->setData(self::DEBIT_AMOUNT, $debitAmount);
    }

    /**
     * Order Increment Id
     *
     * @return string|null
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
    }

    /**
     * Order Increment Id
     *
     * @param string $incrementId IncrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Get available balance
     *
     * @return float|null
     */
    public function getAvailableBalance()
    {
        return $this->_get(self::AVAILABLE_BALANCE);
    }

    /**
     * Get credit limit
     *
     * @return float|null
     */
    public function getCreditLimit()
    {
        return $this->_get(self::CREDIT_LIMIT);
    }

    /**
     * Set customer id
     *
     * @param int $customerId CustomerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set available balance
     *
     * @param float $availableBalance AvailableBalance
     *
     * @return $this
     */
    public function setAvailableBalance($availableBalance)
    {
        return $this->setData(self::AVAILABLE_BALANCE, $availableBalance);
    }

    /**
     * Set credit limit
     *
     * @param float $creditLimit CreditLimit
     *
     * @return $this
     */
    public function setCreditLimit($creditLimit)
    {
        return $this->setData(self::CREDIT_LIMIT, $creditLimit);
    }
}
