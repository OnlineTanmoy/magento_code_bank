<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\CustomerSpecialPrice;

use Appseconnect\B2BMage\Model\ResourceModel\CustomerFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory;
use Magento\Customer\Model\Session;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var CustomerFactory
     */
    public $specialPriceResourceFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\CustomerFactory
     */
    public $specialPriceCollectionFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory
     */
    public $specialPriceProductCollectionFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    public $httpContext;

    public $divisionHelper;

    public $customerFactory;

    public $customerSession;

    public $helperContactPerson;

    /**
     * Data constructor.
     *
     * @param CollectionFactory                                                 $specialPriceProductCollectionFactory SpecialPriceProductCollectionFactory
     * @param CustomerFactory                                                   $specialPriceResourceFactory          SpecialPriceResourceFactory
     * @param \Appseconnect\B2BMage\Model\CustomerFactory                       $specialPriceCollectionFactory        SpecialPriceCollectionFactory
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollectionFactory      PricelistPriceCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory    $productCollectionFactory             ProductCollectionFactory
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data                       $helperPricelist                      HelperPricelist
     * @param \Magento\Store\Model\StoreManagerInterface                        $storeManager                         StoreManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                 $customerRepository                   CustomerRepository
     */
    public function __construct(
        CollectionFactory $specialPriceProductCollectionFactory,
        CustomerFactory $specialPriceResourceFactory,
        \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCollectionFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Framework\App\Http\Context $httpContext,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
    ) {
    
        $this->specialPriceResourceFactory = $specialPriceResourceFactory;
        $this->specialPriceCollectionFactory = $specialPriceCollectionFactory;
        $this->specialPriceProductCollectionFactory = $specialPriceProductCollectionFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperPricelist = $helperPricelist;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->httpContext = $httpContext;
        $this->divisionHelper = $divisionHelper;
    }

    /**
     * GetAssignedCustomerId
     *
     * @param int $specialPriceId SpecialPriceId
     * @param int $customerId     CustomerId
     *
     * @return array
     */
    public function getAssignedCustomerId($specialPriceId = null, $customerId = null)
    {
        $customerCollection = $this->specialPriceCollectionFactory->create()->getCollection();
        if ($customerId) {
            $customerCollection->addFieldToFilter('customer_id', $customerId);
        }
        
        $output = $customerCollection->getData();
        if ($customerId) {
            return $output;
        }
        
        $result = [];
        
        foreach ($output as $val) {
            if ($specialPriceId != $val['id']) {
                $result[] = $val['customer_id'];
            }
        }
        
        return $result;
    }

    /**
     * GetSpecialPriceProducts
     *
     * @param int $id Id
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Specialprice\CollectionFactory
     */
    public function getSpecialPriceProducts($id)
    {
        $productCollection = $this->specialPriceProductCollectionFactory->create();
        $productCollection->addFieldToFilter('parent_id', $id);
        return $productCollection;
    }

    /**
     * GetSpecialPrice
     *
     * @param int    $productId  ProductId
     * @param string $productSku ProductSku
     * @param int    $customerId CustomerId
     * @param int    $websiteId  WebsiteId
     * @param float  $finalPrice FinalPrice
     *
     * @return null|boolean
     */
    public function getSpecialPrice($productId, $productSku, $customerId, $websiteId, $finalPrice)
    {
        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');

        if ($customerType == 3) {
            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                if (isset($currentCustomerId)) {

                    // For division specific discount
                    $divisionCustomerId = $currentCustomerId;
                    $getCustomerDetails = $this->getCustomerDetails($divisionCustomerId);
                    $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                    $customerId = $getCustomerDetails['customerId'];

                } else {
                    $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
                    $customerPricelistCode = $customerCollection->getData('pricelist_code');
                    $customerId = $customerDetail['customer_id'];
                }

            } else {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                // For division specific discount
                $divisionCustomerId = $customerDetail['customer_id'];
                $getCustomerDetails = $this->getCustomerDetails($divisionCustomerId);
                $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                $customerId = $getCustomerDetails['customerId'];
            }
        }

        $specialPrice = '';
        $specialPriceResourceModel = $this->specialPriceResourceFactory->create();
        
        $customerSpecialPriceCollection = $this->specialPriceCollectionFactory->create()->getCollection();
        
        $customerSpecialPriceCollection = $specialPriceResourceModel->addProductMapCollection(
            $productSku,
            $customerSpecialPriceCollection
        );
        
        $customerSpecialPriceCollection->addFieldToFilter('customer_id', $customerId);
        $customerSpecialPriceCollection->addFieldToFilter('is_active', 1);
        $customerSpecialPriceCollection->addFieldToFilter('website_id', $websiteId);
        $customerSpecialPriceCollection->addFieldToFilter(
            'start_date', [
            'lteq' => date('Y-m-d')
            ]
        );
        $customerSpecialPriceCollection->addFieldToFilter(
            'end_date', [
            'gteq' => date('Y-m-d')
            ]
        );
        
        $specialPriceData = $customerSpecialPriceCollection->getData();
        $specialPriceData = isset($specialPriceData[0])?$specialPriceData[0]:null;

        if ($specialPriceData) {
            $discountType = $specialPriceData['discount_type'];
            $specialPrice = $specialPriceData['special_price'];
            $specialPriceData['pricelist_id'] = $customerPricelistCode;

            $pricelistCollection = $this->pricelistPriceCollectionFactory->create();
            $pricelistCollection->addFieldToFilter('id', $specialPriceData['pricelist_id'])
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('website_id', $websiteId);
            $pricelistData = $pricelistCollection->getData();
            $pricelistData = isset($pricelistData[0])?$pricelistData[0]:null;
            if (! empty($pricelistData)) {
                $finalPrice = $this->helperPricelist->getAmount(
                    $productId,
                    $finalPrice,
                    $specialPriceData['pricelist_id']
                );
            }
            
            if ($discountType) {
                $specialPrice = $finalPrice * (100 - $specialPrice) / 100;
            }
        }
        return $specialPrice;
    }

    public function getCustomerDetails($divisionCustomerId)
    {
        $customer = $this->customerRepository->getById($divisionCustomerId);
        if ($customer->getCustomAttribute('parent_rule_configuration') == null) {
            $parentRuleApplied = 0;
        } else {
            $parentRuleApplied = $customer->getCustomAttribute('parent_rule_configuration')->getValue();
        }

        $mainCustomerId = $this->divisionHelper->getMainCustomerId($divisionCustomerId);

        if ($parentRuleApplied) {
            $customerCollection = $this->customerFactory->create()->load($mainCustomerId);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $mainCustomerId;

        } else {
            $customerCollection = $this->customerFactory->create()->load($divisionCustomerId);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $divisionCustomerId;
        }

        return ['customerPricelistCode' => $customerPricelistCode, 'customerId' => $customerId];
    }
}
