<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Plugin\Checkout\Model;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class DefaultConfigProviderPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DefaultConfigProviderPlugin
{

    /**
     * Mapper
     *
     * @var \Magento\Customer\Model\Address\Mapper
     */
    public $addressMapper;

    /**
     * Config
     *
     * @var \Magento\Customer\Model\Address\Config
     */
    public $addressConfig;

    /**
     * CustomerRepository
     *
     * @var CustomerRepository
     */
    public $customerRepository;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * HttpContext
     *
     * @var HttpContext
     */
    public $httpContext;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Initialize Class variable
     *
     * @param \Magento\Customer\Model\Address\Mapper          $addressMapper       AddressMapper
     * @param \Magento\Customer\Model\Address\Config          $addressConfig       AddressConfig
     * @param HttpContext                                     $httpContext         HttpContext
     * @param CustomerRepository                              $customerRepository  CustomerRepository
     * @param Session                                         $customerSession     CustomerSession
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory     CustomerFactory
     */
    public function __construct(
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        HttpContext $httpContext,
        CustomerRepository $customerRepository,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
    
        $this->addressMapper = $addressMapper;
        $this->addressConfig = $addressConfig;
        $this->httpContext = $httpContext;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }

    /**
     * AfterGetConfig
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject Subject
     * @param array                                         $result  Result
     *
     * @return array
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result)
    {
        if (isset($result) && $result) {
            $result['customerData'] = $this->_getCustomerData();
        }
        return $result;
    }

    /**
     * Retrieve customer data
     *
     * @return array
     */
    private function _getCustomerData()
    {
        $customerData = [];
        if ($this->_isCustomerLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            if ($this->helperContactPerson->isContactPerson($this->customerSession->getCustomer())) {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId; // B2B
            }
            
            $customer = $this->customerRepository->getById($customerId);
            $customerData = $customer->__toArray();
            foreach ($customer->getAddresses() as $key => $address) {
                $customerData['addresses'][$key]['inline'] = $this->_getCustomerAddressInline($address);
            }
        }
        return $customerData;
    }

    /**
     * Set additional customer address data
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address Address
     *
     * @return string
     */
    private function _getCustomerAddressInline($address)
    {
        $builtOutputAddressData = $this->addressMapper->toFlatArray($address);
        return $this->addressConfig->getFormatByCode(\Magento\Customer\Model\Address\Config::DEFAULT_ADDRESS_FORMAT)
            ->getRenderer()
            ->renderArray($builtOutputAddressData);
    }

    /**
     * Check if customer is logged in
     *
     * @return bool @codeCoverageIgnore
     */
    private function _isCustomerLoggedIn()
    {
        return (bool) $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }
}
