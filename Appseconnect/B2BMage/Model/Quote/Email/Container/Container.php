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
namespace Appseconnect\B2BMage\Model\Quote\Email\Container;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Abstract Class Container
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class Container implements IdentityInterface
{

    /**
     * StoreManagerInterface
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Store
     *
     * @var Store
     */
    public $store;

    /**
     * String
     *
     * @var string
     */
    public $customerName;

    /**
     * String
     *
     * @var string
     */
    public $customerEmail;

    /**
     * Container constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig  ScopeConfig
     * @param StoreManagerInterface $storeManager StoreManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Return store configuration value
     *
     * @param string $path    Path
     * @param int    $storeId StoreId
     *
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        $configValue = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return $configValue;
    }

    /**
     * Set current store
     *
     * @param Store $currentStore CurrentStore
     *
     * @return void
     */
    public function setStore(Store $currentStore)
    {
        $this->store = $currentStore;
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        // current store
        if ($this->store instanceof Store) {
            return $this->store;
        }
        return $this->storeManager->getStore();
    }

    /**
     * Set customer name
     *
     * @param string $customerName CustomerName
     *
     * @return void
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * Set customer email
     *
     * @param string $customerEmail CustomerEmail
     *
     * @return void
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    /**
     * Return customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Return customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }
}
