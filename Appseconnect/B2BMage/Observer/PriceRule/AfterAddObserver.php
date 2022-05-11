<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\PriceRule;

use Magento\Framework\Event\Observer;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

/**
 * Class AfterAddObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AfterAddObserver implements ObserverInterface
{

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * AfterAddObserver constructor.
     *
     * @param Session $session Session
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param CollectionFactory $pricelistPriceCollectionFactory PricelistPriceCollectionFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule HelperPriceRule
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository CustomerRepository
     */
    public function __construct(
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperPriceRule = $helperPriceRule;
        $this->divisionHelper = $divisionHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void @codeCoverageIgnore
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        $websiteId = $this->customerSession->getCustomer()->getWebsiteId();
        $pricelistPrice = '';

        if ($customerType == 2) {
            return $this;
        }

        $parentRuleApplied = 0;
        if ($customerType != 3) {
            return $this;
        } else {
            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                if (isset($currentCustomerId)) {

                    // For division specific discount
                    $divisionCustomerId = $currentCustomerId;
                    $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
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
                $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
                $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                $customerId = $getCustomerDetails['customerId'];
            }
        }

        $pricelistStatus = null;
        $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();
        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }
        if ($customerId) {
            $item = $observer->getEvent()->getData('quote_item');

            if ($item->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                $this->helperPriceRule->processBundleProduct(
                    $item,
                    $customerPricelistCode,
                    $pricelistStatus,
                    $customerId,
                    $websiteId
                );
            } else {
                $this->helperPriceRule->processSimpleProduct(
                    $item,
                    $customerPricelistCode,
                    $pricelistStatus,
                    $customerId,
                    $websiteId
                );
            }

            return $this;
        }
    }
}
