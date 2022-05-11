<?php
/**
 * Namespace
 *
 * @category ServiceRequest\Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\ServiceRequest\Helper\ServiceRequest;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 *
 * @category ServiceRequest\Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends AbstractHelper
{
    const XML_PATH_INSYNC_CATEGORY_VISIBILITY = 'insync_category_visibility/';


    /**
     * Product
     *
     * @var \Magento\Catalog\Model\Product
     */
    public $product;

    /**
     * QuoteFactory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quote;

    /**
     * QuoteManagement
     *
     * @var \Magento\Quote\Model\QuoteManagement
     */
    public $quoteManagement;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * OrderService
     *
     * @var \Magento\Sales\Model\Service\OrderService
     */
    public $orderService;

    /**
     * AddressFactory
     *
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressFactory;

    /**
     * RequestPostFactory
     *
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    public $serviceRequestPostFactory;

    /**
     * CollectionFactory
     *
     * @var \Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory
     */
    public $requestPostFactory;

    /**
     * Status Collection
     *
     * @var \Appseconnect\ServiceRequest\Model\ResourceModel\Status\Collection
     */
    public $requestStatus;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;

    /**
     * Data constructor.
     *
     * @param Context                                                                        $context                   Context
     * @param \Magento\Catalog\Model\Product                                                 $product                   Product
     * @param \Magento\Quote\Model\QuoteFactory                                              $quote                     Quote
     * @param \Magento\Quote\Model\QuoteManagement                                           $quoteManagement           QuoteManagement
     * @param \Magento\Customer\Model\CustomerFactory                                        $customerFactory           CustomerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                              $customerRepository        CustomerRepository
     * @param \Magento\Sales\Model\Service\OrderService                                      $orderService              OrderService
     * @param \Magento\Customer\Model\AddressFactory                                         $addressFactory            AddressFactory
     * @param \Magento\Framework\App\ResourceConnection                                      $resource                  Resource
     * @param \Appseconnect\ServiceRequest\Model\RequestPostFactory                          $serviceRequestPostFactory ServiceRequestPostFactory
     * @param \Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory $requestPostFactory        RequestPostFactory
     * @param \Appseconnect\ServiceRequest\Model\ResourceModel\Status\Collection             $requestStatus             RequestStatus
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                                $contactPersonHelper
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\Product $product,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Appseconnect\ServiceRequest\Model\RequestPostFactory $serviceRequestPostFactory,
        \Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory $requestPostFactory,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Status\Collection $requestStatus,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
    )
    {
        parent::__construct($context);
        $this->product = $product;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->addressFactory = $addressFactory;
        $this->serviceRequestPostFactory = $serviceRequestPostFactory;
        $this->requestPostFactory = $requestPostFactory;
        $this->requestStatus = $requestStatus;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->_tableEntity = $resource->getTableName('core_config_data');
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->stockRegistry = $stockRegistry;
        $this->customerCollection = $customerCollection;
        $this->contactPersonHelper = $contactPersonHelper;
    }

    /**
     * GetConfigValue
     *
     * @param $field   Field
     * @param null $storeId StoreId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * GetLastNumber
     *
     * @param $path Path
     *
     * @return mixed
     */
    public function getLastNumber($path)
    {
        $sql = "select value from " . $this->_tableEntity .
            " where path ='" . $path . "' and scope='default' and scope_id=0 LIMIT 1";
        return $this->_connection->fetchOne($sql);
    }

    /**
     * SetLastNumber
     *
     * @param $path  Path
     * @param $value Value
     *
     * @return void
     */
    public function setLastNumber($path, $value)
    {
        $sql = "update " . $this->_tableEntity .
            " set value = " . $value .
            " where path ='" . $path . "' and scope='default' and scope_id=0";
        $this->_connection->query($sql);
    }

    /**
     * GetGeneralConfig
     *
     * @param $code    Code
     * @param null $storeId StoreId
     *
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_INSYNC_CATEGORY_VISIBILITY . 'service_document/' . $code, $storeId);
    }

    /**
     * CreateOrder
     *
     * @param $productSku     ProductSku
     * @param $price          Price
     * @param $serviceRequest ServiceRequest
     *
     * @return mixed
     */
    public function createOrder($productSku, $price, $serviceRequest)
    {
        $this->checkoutSession->setStartServiceOrder(1);
        $customer = $this->customerFactory->create();
        $customer->load($serviceRequest->getCustomerId());// load customet by email address


        $parentCustomerId=$this->contactPersonHelper->getCustomerId($serviceRequest->getCustomerId());

        if($parentCustomerId){
            $parentCustomerId = $this->customerFactory->create()->load($parentCustomerId['customer_id']);
        }
        else{
            $parentCustomer = $this->customerFactory->create()->load($serviceRequest->getCustomerId());
        }
        //billing
        $billingAddressId = $parentCustomer->getDefaultBilling();
        $billingAddress = $this->addressFactory->create()->load($billingAddressId);


        //shipping
        $shippingAddress = $this->addressFactory->create()->load($serviceRequest->getShippingAddressId());

        $store = $customer->getStore();


        $quote = $this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly
        $customer = $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        //add items in quote
        $product = $this->product->loadByAttribute('sku', $productSku);

        $quote->addProduct(
            $product,
            intval(1)
        );

        //Set Address to quote
        $quote->getBillingAddress()->addData($billingAddress->getData());
        $quote->getShippingAddress()->addData($shippingAddress->getData());

        foreach ($quote->getAllItems() as $item) {
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);

            //Enable super mode on the product.
            $item->getProduct()->setIsSuperMode(true);
            $quote->save();
        }

        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('freeshipping_freeshipping'); //shipping method
        $quote->setPaymentMethod('creditlimit'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $order->setCanSendNewEmailFlag(false);
        $order->setOrderType("Service");
        $order->setServiceId($serviceRequest->getId());
        $order->setServiceNumber($serviceRequest->getRaId());
        $order->setPurchaseOrderNumber($serviceRequest->getPurchaseOrderNumber())->save();

        // update serviceModel
        $serviceRequest
            ->setOrderId($order->getId())
            ->setBillingAddressId($billingAddressId)
            ->save();

        // reset the service product to 0 inventory
        $stockItem = $this->stockRegistry->getStockItemBySku($productSku);
        $stockItem->setQty(0);
        $stockItem->setIsInStock((bool)0);
        $this->stockRegistry->updateStockItemBySku($productSku, $stockItem);

        $this->checkoutSession->unsStartServiceOrder();

        return  $order;
    }

    /**
     * Return Service request details
     *
     * @param int $requestId Service request ID
     *
     * @return array
     */
    public function getRequestFormDetail($requestId)
    {
        $collection = $this->requestPostFactory->create()
            ->addFieldToFilter('entity_id', $requestId);

        $collection->getSelect()->joinLeft(
            array("s_status" => "insync_service_status"),
            'main_table.status = s_status.id',
            ['s_status.label as status_label']
        );

        if ($collection) {
            return $collection->getFirstItem()->getData();
        } else {
            return [];
        }
    }

    /**
     * GetRequestStatus
     *
     * @return mixed
     */
    public function getRequestStatus()
    {
        return $this->requestStatus;
    }

    /**
     * Get Additional Address
     * @param false $_defaultBilling
     * @param false $_defaultShipping
     * @param false $allAddress
     * @return array|mixed|string
     */
    public function getAdditionalAddresses($_defaultBilling = false, $_defaultShipping = false, $allAddress = false)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerFactory->create()->load($customerId);
        $primatyIds = [$customer->getDefaultShipping(), $customer->getDefaultBilling()];

        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $primatyIds = [$customer->getDefaultShipping(), $customer->getDefaultBilling()];
            $addressesCollection = $customer->getAddresses();
        }

        $addresses = [];
        if ($_defaultBilling) {
            $addresses = '';
            foreach ($addressesCollection as $address) {
                if (in_array($address->getId(), [$customer->getDefaultBilling()])) {
                    $addresses = $address;
                    break;
                }
            }
        } elseif ($_defaultShipping) {
            $addresses = '';
            foreach ($addressesCollection as $address) {
                if (in_array($address->getId(), [$customer->getDefaultShipping()])) {
                    $addresses = $address;
                    break;
                }
            }
        } elseif ($allAddress) {
            // put default shipping on top
            $defaultShipId = 0;
            foreach ($addressesCollection as $address) {
                if (in_array($address->getId(), [$customer->getDefaultShipping()])) {
                    $defaultShipId = $address->getId();
                    $addresses[] = $address;
                    break;
                }
            }

            // now add rest of the address
            foreach ($addressesCollection as $address) {
                if ($address->getId() == $defaultShipId) {
                    continue;
                }
                $addresses[] = $address;
            }
        } else {
            foreach ($addressesCollection as $address) {
                if (!in_array($address->getId(), $primatyIds)) {
                    $addresses[] = $address;
                }
            }
        }
        return $addresses;
    }


}
