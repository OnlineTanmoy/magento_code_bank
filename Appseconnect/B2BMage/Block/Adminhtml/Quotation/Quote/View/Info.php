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

namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\View;

use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Address;

/**
 * Class Info
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Info extends \Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\AbstractQuote
{

    /**
     * Customer service
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    public $metadata;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    public $groupRepository;

    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    public $metadataElementFactory;

    /**
     * @var Address\Renderer
     */
    public $addressRenderer;

    /**
     * TimezoneInterface
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public $timezone;

    /**
     * Info constructor.
     *
     * @param \Magento\Backend\Block\Template\Context              $context         Context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone        Timezone
     * @param \Magento\Framework\Registry                          $registry        Registry
     * @param \Magento\Sales\Helper\Admin                          $adminHelper     AdminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface       $groupRepository GroupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface      $metadata        Metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory      $elementFactory  ElementFactory
     * @param Address\Renderer                                     $addressRenderer AddressRenderer
     * @param array                                                $data            Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->timezone = $timezone;
        $this->metadata = $metadata;
        $this->metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please correct the parent block for this block.')
            );
        }
        $this->setOrder(
            $this->getParentBlock()
                ->getQuote()
        );

        foreach ($this->getParentBlock()->getQuoteInfoData() as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }

        parent::_beforeToHtml();
    }

    /**
     * Get quote store name
     *
     * @return null|string
     */
    public function getQuoteStoreName()
    {
        if ($this->getQuote()) {
            $storeId = $this->getQuote()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getQuote()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName()
            ];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getQuote()) {
            $customerGroupId = $this->getQuote()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if (!$this->getQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl(
            'customer/index/edit',
            [
                'id' => $this->getQuote()
                    ->getCustomerId()
            ]
        );
    }

    /**
     * Get order view URL.
     *
     * @param int $orderId OrderId
     *
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl(
            'sales/order/view',
            [
                'order_id' => $orderId
            ]
        );
    }

    /**
     * Check if is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @param mixed  $store     Store
     * @param string $createdAt CreatedAt
     *
     * @return \DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get timezone for store
     *
     * @param mixed $store Store
     *
     * @return string
     */
    public function getTimezoneForStore($store)
    {
        return $this->_localeDate
            ->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
    }

    /**
     * Get object created at date
     *
     * @param string $createdAt CreatedAt
     *
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date($this->timezone->date($createdAt));
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address Address
     *
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }

    /**
     * GetChildHtml
     *
     * @param string $alias    Alias
     * @param bool   $useCache UseCache
     *
     * @return string
     */
    public function getChildHtml($alias = '', $useCache = true)
    {
        $layout = $this->getLayout();

        if ($alias || !$layout) {
            return parent::getChildHtml($alias, $useCache);
        }

        $childNames = $layout->getChildNames($this->getNameInLayout());
        $outputChildNames = array_diff(
            $childNames,
            [
                'extra_customer_info'
            ]
        );

        $output = '';
        foreach ($outputChildNames as $childName) {
            $output .= $layout->renderElement($childName, $useCache);
        }

        return $output;
    }

    /**
     * GetStore
     *
     * @param int $storeId StoreId
     *
     * @return $this
     */
    public function getStore($storeId)
    {
        if ($storeId) {
            return $this->_storeManager->getStore($storeId);
        }
        return $this->_storeManager->getStore();
    }
}
