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
namespace Appseconnect\B2BMage\Api\Salesrep;

/**
 * Interface SalesrepRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface SalesrepRepositoryInterface
{
    
    /**
     * Create salesrep account. Perform necessary business operations like sending email.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $salesrepData salesrep data
     * @param string                                       $password     password
     * @param string                                       $redirectUrl  redirect url
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccount(
        \Magento\Customer\Api\Data\CustomerInterface $salesrepData,
        $password = null,
        $redirectUrl = ''
    );
    
    /**
     * Update a salesrep.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $salesrepData salesrep data
     * @param string                                       $passwordHash password hash
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $salesrepData, $passwordHash = null);
    
    /**
     * Assign a salesrep to customers.
     *
     * @param \Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface $entity sales customer assign object
     *
     * @return \Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignCustomer(\Appseconnect\B2BMage\Api\Salesrep\Data\SalesrepCustomerAssignInterface $entity);
    
    /**
     * Retrieve customers which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerData(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);


    /**
     * Get Company
     *
     * @param int                                            $id             salesrep id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     * 
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCompany($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
