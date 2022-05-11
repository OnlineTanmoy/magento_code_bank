<?php

namespace Appseconnect\B2BMage\Model\Customer;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Indexer\StateInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Math\Random;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\Session;

/**
 * Class Submit
 * @package Appseconnect\ServiceRequest\Controller\Request
 */
class Customer extends \Magento\Customer\Model\Customer
{
    public $httpContext;

    public $customerFactory;

    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceCustomer $resource
     * @param Share $configShare
     * @param AddressFactory $addressFactory
     * @param CollectionFactory $addressesFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param CustomerMetadataInterface $metadataService
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param AccountConfirmation|null $accountConfirmation
     * @param Random|null $mathRandom
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Session $customerSession
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\ResourceModel\Customer $resource,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressesFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        AccountConfirmation $accountConfirmation = null,
        Random $mathRandom = null,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelperData,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->metadataService = $metadataService;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_configShare = $configShare;
        $this->_addressFactory = $addressFactory;
        $this->_addressesFactory = $addressesFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_groupRepository = $groupRepository;
        $this->_encryptor = $encryptor;
        $this->dateTime = $dateTime;
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->indexerRegistry = $indexerRegistry;
        $this->accountConfirmation = $accountConfirmation ?: ObjectManager::getInstance()
            ->get(AccountConfirmation::class);
        $this->mathRandom = $mathRandom ?: ObjectManager::getInstance()->get(Random::class);
        $this->contactHelperData = $contactHelperData;
        $this->httpContext = $httpContext;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $scopeConfig,
            $resource,
            $configShare,
            $addressFactory,
            $addressesFactory,
            $transportBuilder,
            $groupRepository,
            $encryptor,
            $dateTime,
            $customerDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $metadataService,
            $indexerRegistry,
            $resourceCollection,
            $data,
            $accountConfirmation,
            $mathRandom
        );
    }

    /**
     * Customer addresses collection
     *
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressesCollection()
    {
        $customerId = $this->httpContext->getValue('customer_id');
        $customer = $this->customerFactory->create()->load($customerId);

        if (!$this->contactHelperData->isContactPerson($customer)) {
            return parent::getAddressesCollection();
        } else {
            $actionType = $this->customerSession->getActionType();
            if (isset($actionType) && $actionType == 'division-create') {
                return parent::getAddressesCollection();
            } else {
                $currentCustomerId = $this->customerSession->getCurrentCustomerId();

                $parentCustomerId = $this->contactHelperData->getCustomerId($customerId);
                $parentCustomer = $this->contactHelperData->customerFactory->create()->load($parentCustomerId['customer_id']);

                if ($currentCustomerId) {
                    $parentCustomer = $this->contactHelperData->customerFactory->create()->load($currentCustomerId);
                }

                $this->_addressesCollection = $parentCustomer->getAddressCollection()->setCustomerFilter(
                    $parentCustomer
                )->addAttributeToSelect(
                    '*'
                );
                foreach ($this->_addressesCollection as $address) {
                    $address->setCustomer($parentCustomer);
                }

                return $this->_addressesCollection;
            }
        }
    }

}
