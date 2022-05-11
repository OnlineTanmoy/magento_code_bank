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

namespace Appseconnect\B2BMage\Model\ResourceModel;

use Appseconnect\B2BMage\Api\Company\CompanyRepositoryInterface;
use Magento\Catalog\Model\CategoryList;
use Magento\Catalog\Model\CategoryManagement;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SortOrder;

/**
 * Class CompanyRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CompanyRepository implements CompanyRepositoryInterface
{

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * ProductRepository
     *
     * @var ProductRepository
     */
    public $productRepository;


    /**
     * CategoryList
     *
     * @var CategoryList
     */
    public $categoryList;

    /**
     * CategoryManagement
     *
     * @var CategoryManagement
     */
    public $categoryManagement;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CustomerSearchResultsInterfaceFactory
     *
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * CollectionFactory
     *
     * @var Price\CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryDiscount\Data
     */
    public $helperCategory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data
     */
    public $helperCustomerSpecialPrice;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;

    /**
     * ProductFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * ProductAssign
     *
     * @var \Appseconnect\B2BMage\Model\Data\ProductAssign
     */
    public $productAssign;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * CompanyRepository constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                  $helperContactPerson             HelperContactPerson
     * @param ProductRepository                                                $productRepository               ProductRepository
     * @param CategoryList                                                     $categoryList                    CategoryList
     * @param CategoryManagement                                               $categoryManagement              CategoryManagement
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory                 CustomerFactory
     * @param \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory            SearchResultsFactory
     * @param Price\CollectionFactory                                          $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data               $helperCategory                  HelperCategory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data                      $helperPricelist                 HelperPricelist
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data              $helperTierprice                 HelperTierprice
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data           $helperCustomerSpecialPrice      HelperCustomerSpecialPrice
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data                      $helperPriceRule                 HelperPriceRule
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory                  ProductFactory
     * @param \Appseconnect\B2BMage\Model\Data\ProductAssign                   $productAssign                   ProductAssign
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                $customerRepository              CustomerRepository
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        ProductRepository $productRepository,
        CategoryList $categoryList,
        CategoryManagement $categoryManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Appseconnect\B2BMage\Model\Data\ProductAssign $productAssign,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->productRepository = $productRepository;
        $this->categoryList = $categoryList;
        $this->categoryManagement = $categoryManagement;
        $this->customerFactory = $customerFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->helperPricelist = $helperPricelist;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPriceRule = $helperPriceRule;
        $this->productFactory = $productFactory;
        $this->productAssign = $productAssign;
        $this->customerRepository = $customerRepository;
    }

    /**
     * GetProducts
     *
     * @param int                                            $id             Id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProducts($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $searchResult = $this->productRepository->getList($searchCriteria);

        $products = array();
        foreach ($searchResult->getItems() as $item) {
            $actualPrice = $this->getActualPrice($item, $id);

            $extensionAttributes = $item->getExtensionAttributes();

            $stock = $extensionAttributes->getStockItem();
            $extensionAttributes->setCustomPrice($actualPrice);
            $extensionAttributes->setIsInStock($stock->getIsInStock());
            $item->setExtensionAttributes($extensionAttributes);

            $products[] = $item;
        }

        $searchResult->setItems($products);

        return $searchResult;
    }

    /**
     * GetCategories
     *
     * @param $id             Id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return \Magento\Catalog\Api\Data\CategorySearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategories($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $searchResult = $this->categoryList->getList($searchCriteria);
        return $searchResult;
    }

    /**
     * GetAllCategories
     *
     * @param int  $id             Id
     * @param null $rootCategoryId RootCategoryId
     * @param null $depth          Depth
     *
     * @return \Appseconnect\B2BMage\Api\Catalog\Data\CustomTreeInterface
     */
    public function getAllCategories($id, $rootCategoryId = null, $depth = null)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $searchResult = $this->categoryManagement->getTree($rootCategoryId, $depth);
        return $searchResult;
    }

    /**
     * GetProductBySku
     *
     * @param int    $id          Id
     * @param string $sku         Sku
     * @param bool   $editMode    EditMode
     * @param null   $storeId     StoreId
     * @param bool   $forceReload ForceReload
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|null
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($id, $sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $_product = $this->productRepository->get($sku, $editMode, $storeId, $forceReload);
        $actualPrice = $this->getActualPrice($_product, $id);

        /**
         * Start for tier price
         **/
        $customer = $this->customerFactory->create()->load($id);
        $websiteId = $customer->getWebsiteId();
        $tierpriceCollection = $this->helperTierprice->tierProductCollectionFactory->create()
            ->addFieldToFilter('customer_id', $id)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('website_id', $websiteId);

        $tierpriceCollection->getSelect()
            ->where("map.product_sku = ?", $sku)
            ->order('map.quantity  DESC')
            ->join(
                ['map' => $tierpriceCollection->getResource()->getTable('insync_tierprice_map')],
                'main_table.id = map.parent_id',
                [
                    'parent_id' => 'parent_id',
                    'quantity' => 'quantity',
                    'tier_price' => 'tier_price'
                ]
            );
        $tierpriceData = $tierpriceCollection->getData();
        /**
         * End for tier price
         **/

        $extensionAttributes = $_product->getExtensionAttributes();
        $extensionAttributes->setCustomPrice($actualPrice);
        $extensionAttributes->setTirePrice($tierpriceData);
        $_product->setExtensionAttributes($extensionAttributes);

        return $_product;
    }


    /**
     * GetContactperson
     *
     * @param int                                            $id             Id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria SearchCriteria
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getContactperson($id, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $contactPerson = $this->helperContactPerson->getContactPersonId($id);
        $contactPersonIds = array_column($contactPerson, 'contactperson_id');

        $collection = $this->customerFactory->create()->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $contactPersonIds));

        if ($searchCriteria) {
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            // Add filters from root filter group to the collection
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $result = $this->addFilterCustomerData($group, $collection);
            }

            $searchResults->setTotalCount($collection->getSize());
            $sortOrders = $searchCriteria->getSortOrders();
            if ($sortOrders) {
                foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                    $collection
                        ->addOrder(
                            $sortOrder->getField(),
                            ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                        );
                }
            }
            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        $customers = array();

        foreach ($collection as $customerModel) {
            $customerModelObj = $customerModel->getDataModel();
            $customers[] = $customerModelObj;
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }

    /**
     * AddFilterCustomerData
     *
     * @param \Magento\Framework\Api\Search\FilterGroup                 $filterGroup FilterGroup
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection  Collection
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function addFilterCustomerData(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = [
                'attribute' => $filter->getField(),
                $condition => $filter->getValue()
            ];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
        return $collection;
    }

    /**
     * GetActualPrice
     *
     * @param $_product   _product
     * @param $customerId CustomerId
     *
     * @return float|int|mixed|string
     */
    public function getActualPrice($_product, $customerId)
    {
        /**
         * Start for custom price
         **/
        $productId = $_product->getId();
        $productObject = $this->productFactory->create()
            ->load($productId);

        $customer = $this->customerFactory->create()->load($customerId);
        $websiteId = $customer->getWebsiteId();
        $customerPricelistCode = $customer->getData('pricelist_code');

        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }

        $qtyItem = 1;
        $finalPrice = $productObject
            ->getPrice();
        $actualPrice = $finalPrice;
        if ($customerId) {
            $pricelistPrice = '';
            if ($customerPricelistCode && $pricelistStatus) {
                $pricelistPrice = $this->helperPricelist->getAmount(
                    $productId,
                    $finalPrice,
                    $customerPricelistCode,
                    true
                );
            }

            $categoryIds = $productObject
                ->getCategoryIds();
            $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
                $finalPrice,
                $customerId,
                $categoryIds
            );

            // for tier price
            $tierPrice = '';
            $productSku = $_product->getSku();

            $tierPrice = $this->helperTierprice->getTierprice(
                $productId,
                $productSku,
                $customerId,
                $websiteId,
                $qtyItem,
                $finalPrice
            );

            $specialPrice = '';
            $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                $productId,
                $productSku,
                $customerId,
                $websiteId,
                $finalPrice
            );

            if ($productObject->getTypeId() != 'bundle' || $productObject->getTypeId() != 'configurable') {
                if ($pricelistPrice) {
                    $finalPrice = $pricelistPrice;
                }
                $actualPrice = $this->helperPriceRule->getActualPrice(
                    $finalPrice,
                    $tierPrice,
                    $categoryDiscountedPrice,
                    $pricelistPrice,
                    $specialPrice
                );
            }
        }
        /**
         * End for custom price
         **/


        return $actualPrice;
    }

    /**
     * GetAddressList
     *
     * @param int $id Id
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]|void
     */
    public function getAddressList($id)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $customer = $this->customerRepository->getById($id);
        $addresses = $customer->getAddresses();

        return $addresses;
    }

    /**
     * SaveAddress
     *
     * @param int                                          $id           Id
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer     Customer
     * @param null                                         $passwordHash PasswordHash
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|void
     */
    public function saveAddress($id, \Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $customer = $this->customerRepository->save($customer, $passwordHash);
        return $customer;
    }

    /**
     * GetCompany
     *
     * @param int $id Id
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCompany($id)
    {
        if (!$this->helperContactPerson->isB2Bcustomer($id)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("Request company doesn't exist", $id)
            );
        }

        $customer = $this->customerRepository->getById($id);
        return $customer;
    }
}
