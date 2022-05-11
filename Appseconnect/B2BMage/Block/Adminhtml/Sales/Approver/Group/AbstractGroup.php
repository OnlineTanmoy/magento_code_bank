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
namespace Appseconnect\B2BMage\Block\Adminhtml\Sales\Approver\Group;

use Appseconnect\B2BMage\Model\ResourceModel\Approver\CollectionFactory;
use Appseconnect\B2BMage\Model\ResourceModel\ApproverFactory;
use Magento\Backend\Block\Widget;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

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
     * Data
     *
     * @var \Magento\Directory\Helper\Data
     */
    public $directoryHelper;

    /**
     * GroupRepositoryInterface
     *
     * @var GroupRepositoryInterface
     */
    public $groupRepository;

    /**
     * GroupManagementInterface
     *
     * @var GroupManagementInterface
     */
    public $groupManagement;

    /**
     * SearchCriteriaBuilder
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * CurrencyInterface
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    public $localeCurrency;

    /**
     * ApproverFactory
     *
     * @var ApproverFactory
     */
    public $approverResourceFactory;

    /**
     * CustomerFactory
     *
     * @var CustomerFactory
     */
    public $customerFactory;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $approverCollectionFactory;

    /**
     * AbstractGroup constructor.
     *
     * @param \Magento\Backend\Block\Template\Context      $context                   Context
     * @param ApproverFactory                              $approverResourceFactory   ApproverResourceFactory
     * @param CustomerFactory                              $customerFactory           CustomerFactory
     * @param CollectionFactory                            $approverCollectionFactory ApproverCollectionFactory
     * @param GroupRepositoryInterface                     $groupRepository           GroupRepository
     * @param \Magento\Directory\Helper\Data               $directoryHelper           DirectoryHelper
     * @param \Magento\Framework\Module\Manager            $moduleManager             ModuleManager
     * @param \Magento\Framework\Registry                  $registry                  Registry
     * @param GroupManagementInterface                     $groupManagement           GroupManagement
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder     SearchCriteriaBuilder
     * @param \Magento\Framework\Locale\CurrencyInterface  $localeCurrency            LocaleCurrency
     * @param array                                        $data                      Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ApproverFactory $approverResourceFactory,
        CustomerFactory $customerFactory,
        CollectionFactory $approverCollectionFactory,
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
        $this->approverResourceFactory = $approverResourceFactory;
        $this->customerFactory = $customerFactory;
        $this->directoryHelper = $directoryHelper;
        $this->moduleManager = $moduleManager;
        $this->approverCollectionFactory = $approverCollectionFactory;
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Element
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Element
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
            $value['readonly'] = $value['website_id'] == 0 &&
                $this->isShowWebsiteColumn() &&
                !$this->isAllowChangeWebsite();
            $value['price'] = $currency->toCurrency($value['price'], ['display' => \Magento\Framework\Currency::NO_SYMBOL]);
        }

        return $values;
    }

    /**
     * Sort values
     *
     * @param array $data Data
     *
     * @return array
     */
    private function _sortValues($data)
    {
        return $data;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int|null $groupId return name by customer group id
     *
     * @return array|string
     */
    public function getCustomerGroups($groupId = null)
    {
        if ($this->customerGroups === null) {
            if (!$this->moduleManager->isEnabled('Magento_Customer')) {
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
        return !$this->_storeManager->isSingleStoreMode();
    }

    /**
     * Retrieve allowed for edit websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $this->websites = [
            0 => ['name' => 'All Websites', 'currency' => $this->directoryHelper->getBaseCurrencyCode()]
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
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return $this->_storeManager->getStore($this->getProduct()->getStoreId())->getWebsiteId();
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
     * @param string $defaultValue DefaultValue
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
     * @param string $defaultValue DefaultValue
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
