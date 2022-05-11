<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerTierPrice\Edit\Tab\View\Group;

use Magento\Backend\Block\Widget;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Tierprice\CollectionFactory;

/**
 * Abstract Class AbstractGroup
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
abstract class AbstractGroup extends Widget implements RendererInterface
{

    /**
     * Form element instance
     *
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public $element;

    /**
     * Customer groups cache
     *
     * @var array
     */
    public $customerGroups;

    /**
     * Websites cache
     *
     * @var array
     */
    public $websites;

    /**
     * Catalog data
     *
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Group repository
     *
     * @var GroupRepositoryInterface
     */
    public $groupRepository;

    /**
     * Group management
     *
     * @var GroupManagementInterface
     */
    public $groupManagement;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * Local currency
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    public $localeCurrency;

    /**
     * Tier price helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $tierPriceHelper;

    /**
     * Tier price collection
     *
     * @var CollectionFactory
     */
    public $tierPriceCollectionFactory;

    /**
     * Directory helper
     *
     * @var \Magento\Directory\Helper\Data
     */
    public $directoryHelper;

    /**
     * AbstractGroup constructor.
     *
     * @param \Magento\Backend\Block\Template\Context             $context                    context
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $tierPriceHelper            tier price helper
     * @param CollectionFactory                                   $tierPriceCollectionFactory tier price collection
     * @param GroupRepositoryInterface                            $groupRepository            gorup repository
     * @param \Magento\Directory\Helper\Data                      $directoryHelper            directory helper
     * @param \Magento\Framework\Module\Manager                   $moduleManager              module manager
     * @param \Magento\Framework\Registry                         $registry                   registry
     * @param GroupManagementInterface                            $groupManagement            group management
     * @param \Magento\Framework\Api\SearchCriteriaBuilder        $searchCriteriaBuilder      search criteria
     * @param \Magento\Framework\Locale\CurrencyInterface         $localeCurrency             local currency
     * @param array                                               $data                       data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $tierPriceHelper,
        CollectionFactory $tierPriceCollectionFactory,
        GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->tierPriceHelper = $tierPriceHelper;
        $this->tierPriceCollectionFactory = $tierPriceCollectionFactory;
        $this->directoryHelper = $directoryHelper;
        $this->moduleManager = $moduleManager;
        $this->coreRegistry = $registry;
        $this->groupManagement = $groupManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Render HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set form element instance
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element element
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
     */
    public function setElement(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Prepare group price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $data = $this->getElement()->getValue();
        
        if (is_array($data)) {
            $values = $this->_sortValues($data);
        }
        
        $currency = $this->localeCurrency->getCurrency($this->directoryHelper->getBaseCurrencyCode());
        
        foreach ($values as &$value) {
            $value['readonly'] = $value['website_id'] == 0
                                    && $this->isShowWebsiteColumn()
                                    && ! $this->isAllowChangeWebsite();
            $value['price'] = $currency->toCurrency(
                $value['price'], [
                'display' => \Magento\Framework\Currency::NO_SYMBOL
                ]
            );
        }
        
        return $values;
    }

    /**
     * Sort values
     *
     * @param array $data data
     *
     * @return array
     */
    public function _sortValues($data)
    {
        return $data;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int|null $groupId group id
     *
     * @return array|string
     */
    public function getCustomerGroups($groupId = null)
    {
        if ($this->customerGroups === null) {
            if (! $this->moduleManager->isEnabled('Magento_Customer')) {
                return [];
            }
            $this->customerGroups = $this->_getInitialCustomerGroups();
            
            $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());
            foreach ($groups->getItems() as $group) {
                $this->customerGroups[$group->getId()] = $group->getCode();
            }
        }
        
        if ($groupId !== null) {
            return isset($this->customerGroups[$groupId]) ? $this->customerGroups[$groupId] : [];
        }
        
        return $this->customerGroups;
    }

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    public function _getInitialCustomerGroups()
    {
        return [];
    }

    /**
     * Retrieve number of websites
     *
     * @return int
     */
    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    /**
     * Show website column and switcher for group price table
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return ! $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Retrieve allowed for edit websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $this->websites = [
            0 => [
                'name' => 'All Websites',
                'currency' => $this->directoryHelper->getBaseCurrencyCode()
            ]
        ];
        
        return $this->_storeManager->getStore(1);
    }

    /**
     * Retrieve default value for customer group
     *
     * @return int
     */
    public function getDefaultCustomerGroup()
    {
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && ! $this->isAllowChangeWebsite()) {
            return $this->_storeManager->getStore(
                $this->getProduct()
                    ->getStoreId()
            )
                ->getWebsiteId();
        }
        return 0;
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getSubmitButton()
    {
        return $this->getChildHtml('submit_button');
    }

    /**
     * Retrieve customized price column header
     *
     * @param string $defaultValue default value
     *
     * @return string
     */
    public function getPriceColumnHeader($defaultValue)
    {
        $priceColumnHeader = $this->hasData('price_column_header');
        if ($priceColumnHeader) {
            return $this->getData('price_column_header');
        } else {
            return $defaultValue;
        }
    }

    /**
     * Retrieve customized price column header
     *
     * @param string $defaultValue default value
     *
     * @return string
     */
    public function getPriceValidation($defaultValue)
    {
        $priceValidation = $this->hasData('price_validation');
        if ($priceValidation) {
            return $this->getData('price_validation');
        } else {
            return $defaultValue;
        }
    }

    /**
     * Retrieve Group Price entity attribute
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public function getAttribute()
    {
        $entityAttribute = $this->getElement()->getEntityAttribute();
        return $entityAttribute;
    }

    /**
     * Check group price attribute scope is global
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        $isScopeGlobal = $this->getAttribute()->isScopeGlobal();
        return $isScopeGlobal;
    }

    /**
     * Show group prices grid website column
     *
     * @return bool
     */
    public function isShowWebsiteColumn()
    {
        $isSingleStoreMode = $this->_storeManager->isSingleStoreMode();
        if ($this->isScopeGlobal() || $isSingleStoreMode) {
            return false;
        }
        return true;
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    public function isAllowChangeWebsite()
    {
        $productStoreId = $this->getProduct()->getStoreId();
        if (!$this->isShowWebsiteColumn() || $productStoreId) {
            return false;
        }
        return true;
    }
}
