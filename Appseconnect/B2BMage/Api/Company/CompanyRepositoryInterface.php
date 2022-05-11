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
namespace Appseconnect\B2BMage\Api\Company;

/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CompanyRepositoryInterface
{
    /**
     * Retrieve customers which match a specified criteria.
     *
     * @param int                                            $id             id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($id,\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve category for perticular company
     *
     * @param int                                            $id             id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Magento\Catalog\Api\Data\CategorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategories($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get product by sku
     *
     * @param int      $id          id
     * @param string   $sku         sku
     * @param bool     $editMode    edit mode
     * @param int|null $storeId     store id
     * @param bool     $forceReload force reload
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($id, $sku, $editMode = false, $storeId = null, $forceReload = false);

    /**
     * Get all categories
     *
     * @param int $id             id
     * @param int $rootCategoryId root category id
     * @param int $depth          depth
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface containing Tree objects
     */
    public function getAllCategories($id, $rootCategoryId = null, $depth = null);


    /**
     * Get contactperson
     *
     * @param int                                            $id             id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContactperson($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);


    /**
     * Get address list
     *
     * @param int $id id
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getAddressList($id);

    /**
     * Create or update a customer.
     *
     * @param int                                          $id           id
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer     customer
     * @param string                                       $passwordHash password hash
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveAddress($id, \Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null);

    /**
     * Create or update a customer.
     *
     * @param int $id id
     * 
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCompany($id);
}
