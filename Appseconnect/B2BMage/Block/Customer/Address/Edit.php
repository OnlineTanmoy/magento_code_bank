<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Block\Customer\Address;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;

/**
 * Interface Edit
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends \Magento\Directory\Block\Data
{
    /**
     * Address
     *
     * @var \Magento\Customer\Api\Data\AddressInterface|null
     */
    public $address = null;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Address repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * Address data
     *
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    public $addressDataFactory;

    /**
     * Current customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    public $currentCustomer;

    /**
     * Data object helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context                 $context                  context
     * @param \Magento\Directory\Helper\Data                                   $directoryHelper          directory helper
     * @param \Magento\Framework\Json\EncoderInterface                         $jsonEncoder              json encoder
     * @param \Magento\Framework\App\Cache\Type\Config                         $configCacheType          config cache type
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory  $regionCollectionFactory  region collection
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory country collection
     * @param Session                                                          $customerSession          customer session
     * @param \Magento\Customer\Api\AddressRepositoryInterface                 $addressRepository        address repository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory               $addressDataFactory       address data
     * @param \Magento\Customer\Helper\Session\CurrentCustomer                 $currentCustomer          cusrrent customer
     * @param \Magento\Framework\Api\DataObjectHelper                          $dataObjectHelper         data object helper
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data                  $helperContactPerson      contact person helper
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory          customer
     * @param array                                                            $data                     data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->addressDataFactory = $addressDataFactory;
        $this->currentCustomer = $currentCustomer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }

    /**
     * Prepare the layout of the address edit block.
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        // Init address object
        if ($addressId = $this->getRequest()->getParam('id')) {
            try {
                $this->address = $this->addressRepository->getById($addressId);
                $customerId = $this->customerSession->getCustomerId();
                if ($this->helperContactPerson->isContactPerson(
                    $this->customerFactory->create()->load($customerId)
                )
                ) {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
                }
                if ($this->address->getCustomerId() != $customerId) {
                    $this->address = null;
                }
            } catch (NoSuchEntityException $e) {
                $this->address = null;
            }
        }

        if ($this->address === null || !$this->address->getId()) {
            $this->address = $this->addressDataFactory->create();
            $customer = $this->getCustomer();
            $this->address->setPrefix($customer->getPrefix());
            $this->address->setFirstname($customer->getFirstname());
            $this->address->setMiddlename($customer->getMiddlename());
            $this->address->setLastname($customer->getLastname());
            $this->address->setSuffix($customer->getSuffix());
        }

        $this->pageConfig->getTitle()->set($this->getAddressTitle());

        if ($postedAddressData = $this->customerSession->getAddressFormData(true)) {
            $postedAddressData['region'] = [
                'region' => $postedAddressData['region'] ? $postedAddressData['region'] : null
            ];
            if (!empty($postedAddressData['region_id'])) {
                $postedAddressData['region']['region_id'] = $postedAddressData['region_id'];
            }
            $this->dataObjectHelper->populateWithArray(
                $this->address,
                $postedAddressData,
                \Magento\Customer\Api\Data\AddressInterface::class
            );
        }

        return $this;
    }

    /**
     * Generate name block html.
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock(\Magento\Customer\Block\Widget\Name::class)
            ->setObject($this->getAddress());

        return $nameBlock->toHtml();
    }

    /**
     * Return the title, either editing an existing address, or adding a new one.
     *
     * @return string
     */
    public function getAddressTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = __('Edit Address');
        } else {
            $title = __('Add New Address');
        }
        return $title;
    }

    /**
     * Return the Url to go back.
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if ($url) {
            return $url;
        }

        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Return the Url for saving.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/formPost',
            ['_secure' => true, 'id' => $this->getAddress()->getId()]
        );
    }

    /**
     * Return the associated address.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getAddress()
    {
        if ($customerAddress = $this->address) {
            return $customerAddress;
        }
        return null;
    }

    /**
     * Return the specified numbered street line.
     *
     * @param int $lineNumber line number
     * 
     * @return string
     */
    public function getStreetLine($lineNumber)
    {
        $addressStreet = $this->address->getStreet();
        return isset($addressStreet[$lineNumber - 1]) ? $addressStreet[$lineNumber - 1] : '';
    }

    /**
     * Return the country Id.
     *
     * @return int|null|string
     */
    public function getCountryId()
    {
        $countryId = $this->getAddress()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Return the name of the region for the address being edited.
     *
     * @return string region name
     */
    public function getRegion()
    {
        $region = $this->getAddress()->getRegion();
        return $region === null ? '' : $region->getRegion();
    }

    /**
     * Return the id of the region being edited.
     *
     * @return int region id
     */
    public function getRegionId()
    {
        $region = $this->getAddress()->getRegion();
        return $region === null ? 0 : $region->getRegionId();
    }

    /**
     * Retrieve the number of addresses associated with the customer given a customer Id.
     *
     * @return int
     */
    public function getCustomerAddressCount()
    {
        $addressCount = count($this->getCustomer()->getAddresses());
        return $addressCount;
    }

    /**
     * Determine if the address can be set as the default billing address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultBilling()
    {
        $addressId = $this->getAddress()->getId();
        if (!$addressId) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }

    /**
     * Determine if the address can be set as the default shipping address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultShipping()
    {
        $addressId = $this->getAddress()->getId();
        if (!$addressId) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();
    }

    /**
     * Is the address the default billing address?
     *
     * @return bool
     */
    public function isDefaultBilling()
    {
        return (bool)$this->getAddress()->isDefaultBilling();
    }

    /**
     * Is the address the default shipping address?
     *
     * @return bool
     */
    public function isDefaultShipping()
    {
        return (bool)$this->getAddress()->isDefaultShipping();
    }

    /**
     * Retrieve the Customer Data using the customer Id from the customer session.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if ($currentCustomer = $this->currentCustomer->getCustomer()) {
            return $currentCustomer;
        }
        return null;
    }

    /**
     * Return back button Url, either to customer address or account.
     *
     * @return string
     */
    public function getBackButtonUrl()
    {
        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Get config value.
     *
     * @param string $path path
     *
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
