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

namespace Appseconnect\B2BMage\Helper\Quotation;

use Magento\Framework\App\Helper\Context;
use Appseconnect\B2BMage\Model\ResourceModel\Quote\CollectionFactoryInterface as QuoteCollectionFactoryInterface;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\Url\Helper\Data
{

    /**
     * Path to controller to delete item from cart
     */
    const DELETE_URL = 'b2bmage/quotation/index_delete';

    const CHECKOUT_URL = 'b2bmage/quotation/index_checkout';

    const XML_PATH_ENABLE_QUOTATION = 'insync_quotes/general/enable_quote';

    const XML_PATH_QUOTATION_LIFETIME = 'insync_quotes/general/lifetime';

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    public $storeManager;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPriceRule;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    public $helperPricelist;

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * StockStateInterface
     *
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    public $stockState;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helper;

    /**
     * Data
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * QuoteCollectionFactoryInterface
     *
     * @var QuoteCollectionFactoryInterface
     */
    protected $quoteCollectionFactory;

    /**
     * QuoteFactory
     *
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    protected $quoteFactory;

    public $httpContext;

    public $cart;

    /**
     * Data constructor.
     *
     * @param Context                                            $context                Context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig            ScopeConfig
     * @param \Magento\Store\Model\Store                         $storeManager           StoreManager
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data        $helperPriceRule        HelperPriceRule
     * @param \Magento\CatalogInventory\Api\StockStateInterface  $stockState             StockState
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data    $helper                 Helper
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper             JsonHelper
     * @param QuoteCollectionFactoryInterface                    $quoteCollectionFactory QuoteCollectionFactory
     * @param \Appseconnect\B2BMage\Model\QuoteFactory           $quoteFactory           QuoteFactory
     * @param \Magento\Customer\Model\Session                    $customerSession        customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface  $customerRepository     customerRepository
     * @param \Magento\Framework\App\Http\Context                $httpContext
     */

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Store $storeManager,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        QuoteCollectionFactoryInterface $quoteCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Checkout\Model\Cart $cart
    )
    {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->helperPriceRule = $helperPriceRule;
        $this->scopeConfig = $scopeConfig;
        $this->stockState = $stockState;
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;
        $this->jsonHelper = $jsonHelper;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteFactory = $quoteFactory;
        $this->httpContext = $httpContext;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * UpdateQuote
     *
     * @return void
     */
    public function updateQuote()
    {
        $enable = $this->isQuotationEnabled();
        if ($enable) {
            try {
                $days = $this->isQuotationLifeTime();
                $currentDate = date('Y-m-d');
                $daysAgo = date('Y-m-d 23:59:59', strtotime('-' . $days . ' days', strtotime($currentDate)));
                $quoteCollection = $this->quoteCollectionFactory
                    ->create()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('status', array('eq' => 'approved'))
                    ->addFieldToFilter('updated_at', array('lteq' => $daysAgo))
                    ->setOrder('updated_at', 'asc');
                foreach ($quoteCollection as $quote) {
                    $quoteObject = $this->quoteFactory->create()->load($quote->getId());
                    $quoteObject->setStatus("closed");
                    $quoteObject->save();
                }

            } catch (\Exception $e) {
            }
        }

    }

    /**
     * getEnableQuoteValue
     *
     * @return int
     */
    public function getEnableQuoteValue()
    {
        $customerId = $this->httpContext->getValue('customer_id');

        if ($customerId)
        {
            $customerType = $this->httpContext->getValue('customer_type');
            if ($customerType == 3) {
                $customerDetail = $this->helper->getCustomerId($customerId);
                $customerId = $customerDetail['customer_id'];
                $customer = $this->customerRepository->getById($customerId);

                if($customer->getCustomAttribute('enable_quote')==null){
                    return 0;
                }

                return $customer->getCustomAttribute('enable_quote')->getValue();
            }
        }
        return 0;
    }

    /**
     * GetQuotationInfo
     *
     * @param array       $data Data
     * @param null|string $type Type
     *
     * @return string|array
     */
    public function getQuotationInfo($data, $type = null)
    {
        if ($type == "json") {
            $result = $this->jsonHelper->jsonEncode($data);
        } else {
            $result = $this->jsonHelper->jsonDecode($data);
        }
        return $result;
    }

    /**
     * isFromQuote
     *
     * @return mixed
     */
    public function isFromQuote(){
        $quote=$this->cart->getQuote();
        return $quote->getQuotationInfo();
    }

    /**
     * Get post parameters for delete from cart
     *
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $item 
     * @return string
     */
    public function getDeletePostJson($item)
    {
        $url = $this->_getUrl(self::DELETE_URL);

        $data = [
            'item_id' => $item->getId()
        ];
        if (!$this->_request->isAjax()) {
            $data[\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = $this->getCurrentBase64Url();
        }
        return json_encode(
            [
                'action' => $url,
                'data' => $data
            ]
        );
    }

    /**
     * GetCheckoutPostJson
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     *
     * @return string
     */
    public function getCheckoutPostJson($quote)
    {
        $url = $this->_getUrl(self::CHECKOUT_URL);

        $data = [
            'quote_id' => $quote->getId()
        ];
        if (!$this->_request->isAjax()) {
            $data[\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = $this->getCurrentBase64Url();
        }
        return json_encode(
            [
                'action' => $url,
                'data' => $data
            ]
        );
    }

    /**
     * IsQuotationLifeTime
     *
     * @return mixed
     */
    public function isQuotationLifeTime()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_QUOTATION_LIFETIME, 'store');
    }

    /**
     * IsQuotationEnabled
     *
     * @return mixed
     */
    public function isQuotationEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_QUOTATION, 'store');
    }

    /**
     * PrepareItems
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote       Quote
     * @param array                             $requestData RequestData
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function prepareItems($quote, $requestData)
    {
        $requestData = $this->suggestItemsQty($quote, $requestData);
        $quoteModel = $this->updateItems($quote, $requestData);
        return $quoteModel;
    }

    /**
     * UpdateItems
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     * @param array                             $data  Data
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateItems($quote, $data)
    {
        $quoteWebsiteId = $this->storeManager->load($quote->getStoreId())
            ->getWebsiteId();

        $qtyRecalculatedFlag = false;
        foreach ($data as $itemId => $itemInfo) {
            $item = $quote->getItemById($itemId);
            if (!$item) {
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (double)$itemInfo['qty'] : false;
            $price = isset($itemInfo['price']) ? (double)$itemInfo['price'] : false;
            if ($qty > 0) {
                $item->setQty($qty);

                $customPrice = $this->helperPriceRule->getDiscountedPrice(
                    $item->getProduct()
                        ->getId(), $item->getQty(), $quote->getContactId(), $quoteWebsiteId
                );
                $item->setPrice($customPrice);
                $item->setBasePrice($customPrice);

                if ($item->getHasError()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                }

                if (isset($itemInfo['before_suggest_qty']) && $itemInfo['before_suggest_qty'] != $qty) {
                    $qtyRecalculatedFlag = true;
                    $this->messageManager->addNotice(
                        __(
                            'Quantity was recalculated from %1 to %2',
                            $itemInfo['before_suggest_qty'],
                            $qty
                        ),
                        'quote_item' . $item->getId()
                    );
                }
            }

            if ($price && $price > 0) {
                $item->setPrice($price);
                $item->setBasePrice($price);

                if ($item->getHasError()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                }
            }
        }

        if ($qtyRecalculatedFlag) {
            $this->messageManager->addNotice(
                __('We adjusted product quantities to fit the required increments.')
            );
        }

        return $quote;
    }

    /**
     * SuggestItemsQty
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     * @param array                             $data  Data
     *
     * @return array
     */
    public function suggestItemsQty($quote, $data)
    {
        foreach ($data as $itemId => $quote) {
            if (!isset($itemInfo['qty'])) {
                continue;
            }
            $qty = (float)$itemInfo['qty'];
            if ($qty <= 0) {
                continue;
            }

            $quoteItem = $quote->getItemById($itemId);
            if (!$quoteItem) {
                continue;
            }

            $product = $quoteItem->getProduct();
            if (!$product) {
                continue;
            }

            $data[$itemId]['before_suggest_qty'] = $qty;
            $data[$itemId]['qty'] = $this->stockState
                ->suggestQty(
                    $product->getId(), $qty, $product->getStore()
                    ->getWebsiteId()
                );
        }
        return $data;
    }

    /**
     * RemoveItem
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote  Quote
     * @param int                               $itemId ItemId
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function removeItem($quote, $itemId)
    {
        $item = $quote->getItemById($itemId);
        if ($item) {
            $item->isDeleted(true);

            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }

            $parent = $item->getParentItem();
            if ($parent) {
                $parent->isDeleted(true);
            }

            if (!$quote->getAllVisibleItems()) {
                $quote->isDeleted(true);
            }
        }

        return $quote;
    }
}
