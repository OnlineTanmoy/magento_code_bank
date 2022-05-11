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
namespace Appseconnect\B2BMage\Api\Quotation;

/**
 * Interface QuotationServiceInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuotationServiceInterface
{
    /**
     * Submit a quote.
     *
     * @param int $id Quote ID.
     *
     * @return bool
     */
    public function submitQuoteById($id);
    
    /**
     * Approve a quote.
     *
     * @param int $id Quote ID.
     *
     * @return bool
     */
    public function approveQuoteById($id);
    
    /**
     * Hold a quote.
     *
     * @param int $id Quote ID.
     *
     * @return bool
     */
    public function holdQuoteById($id);
    
    /**
     * Unhold a quote.
     *
     * @param int $id Quote ID.
     *
     * @return bool
     */
    public function unholdQuoteById($id);
    
    /**
     * Cancel a quote.
     *
     * @param int $id Quote ID.
     * 
     * @return bool
     */
    public function cancelQuoteById($id);
}
