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

use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteSearchResultsInterface;

/**
 * Interface QuotationRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuotationRepositoryInterface
{

    /**
     * Loads a specified quotation.
     *
     * @param int $id id
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     */
    public function get($id);

    /**
     * Get quote by contact Id
     *
     * @param int   $contactId      contact id
     * @param int[] $sharedStoreIds shared store ids
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForContact($contactId, array $sharedStoreIds = []);

    /**
     * Save quotation
     *
     * @param QuoteInterface $quote quote object
     *
     * @return void
     */
    public function save(QuoteInterface $quote);

    /**
     * Retrieve quotes which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Creates an empty quote for a specified contact person.
     *
     * @param int $contactPersonId contact person id
     *
     * @return int Quote ID.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The empty quote could not be created.
     */
    public function createEmptyQuoteForContact($contactPersonId);
}
