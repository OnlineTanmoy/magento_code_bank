<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Controller\QuickOrder\Cart;

use Magento\CatalogInventory\Api\StockConfigurationInterface as StockConfigurationInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;

/**
 * Class ProductSearchList
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductSearchList extends \Magento\Framework\App\Action\Action
{
    /**
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * Stock registry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Category
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * Product collection
     *
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Result
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultFactory;

    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Resources
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * Product repository
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * Stock configuration
     *
     * @var StockConfigurationInterface
     */
    public $stockConfiguration;

    /**
     * Cart
     *
     * @var cart
     */
    public $cart;

    /**
     * Product search list constractor
     *
     * @param \Magento\Framework\App\Action\Context                $context                  context
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory           product
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry            stock registry
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager             store manager
     * @param \Magento\Catalog\Model\CategoryFactory               $categoryFactory          category
     * @param ProductCollectionFactory                             $productCollectionFactory product collection
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory          customer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig              scope
     * @param \Magento\Framework\App\ResourceConnection            $resourceConnection       resource connection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository        product repository
     * @param StockConfigurationInterface                          $stockConfiguration       stock configuration
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory        result json
     * @param \Magento\Checkout\Model\Cart                         $cart                     cart
     * @param Session                                              $customerSession          customer session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        Session $customerSession
    ) {

        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->resultFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->resources = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->stockConfiguration = $stockConfiguration;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Init product
     *
     * @param int $id product id
     *
     * @return \Magento\Catalog\Model\Product|NULL
     */
    private function _initProduct($id)
    {
        if ($id) {
            $model = $this->productFactory->create()->load($id);
            return $model;
        }
        return null;
    }

    /**
     * Searches product using SKU
     *
     * @return void
     */
    public function execute()
    {
        $customerSessionId = $this->customerSession->getCustomerId();

        if (!($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('');
            return $resultRedirect;
        }

        $customerId = $this->customerSession->getCustomerId();
        $allowedCategoryId = [];
        $customerGroupId = $this->customerFactory->create()
            ->load($customerId)
            ->getGroupId();
        $categoryVisibilityStatus = $this->scopeConfig->getValue(
            'insync_category_visibility/select_visibility/select_visibility_type',
            'store'
        );

        $collection = $this->productCollectionFactory->create();

        $collection = $this->_getFilteredCollection($collection, $customerGroupId, $categoryVisibilityStatus);

        $productDetail = [];
        $output = [];

        if ($collection->getData()) {
            foreach ($collection->getData() as $product) {
                $productVisibilityStatus = $this->productRepository->get($product['sku'])->getVisibility();
                if ($productVisibilityStatus == 1) {
                    continue;
                }
                $quantityStatus = $this->stockRegistry->getStockItem($product['entity_id']);
                $storeId = $this->_initProduct($product['entity_id'])->getStoreId();
                $quantity = $quantityStatus['qty'];
                $useConfigMinSaleQty = $quantityStatus['use_config_min_sale_qty'];
                if ($useConfigMinSaleQty == 1 && $this->stockConfiguration->getMinSaleQty(
                    $storeId,
                    $customerGroupId
                )
                ) {
                    $minSaleQty = $this->stockConfiguration->getMinSaleQty($storeId, $customerGroupId);
                } elseif ($useConfigMinSaleQty == 0 && $quantityStatus['min_sale_qty']) {
                    $minSaleQty = $quantityStatus['min_sale_qty'];
                } else {
                    $minSaleQty = 1;
                }
                $qtyIncrements = ($quantityStatus['qty_increments'] &&
                    $quantityStatus['qty_increments'] > 0) ?
                    $quantityStatus['qty_increments'] : 1;
                $productDetail['id'] = json_encode(
                    [
                    'sku' => $product['sku'],
                    'min_qty' => (int)$minSaleQty,
                    'product_id' => $product['entity_id'],
                    'qty_increments' => $qtyIncrements
                    ]
                );
                $productDetail['text'] = $product['sku'];
                $productDetail['qty_increments'] = $qtyIncrements;
                $output[] = $productDetail;
            }
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($output);
    }

    /**
     * Get filter collection
     *
     * @param ProductCollectionFactory $collection               product collection
     * @param int                      $customerGroupId          customer group id
     * @param string                   $categoryVisibilityStatus ategory visivility status
     *
     * @return ProductCollectionFactory
     */
    private function _getFilteredCollection($collection, $customerGroupId, $categoryVisibilityStatus)
    {
        $productSku = $this->getRequest()->getParam('productSku');
        $availableSku = $this->getRequest()->getParam('object');
        if ($availableSku) {
            $skuArray = [];
            foreach ($availableSku['data'] as $sku) {
                $skuArray[] = "'" . $sku['sku'] . "'";
            }
        }
        $allowedCategory = $this->categoryFactory->create()
            ->getCollection()
            ->addAttributeToFilter(
                'customer_group', [
                'like' => '%' . $customerGroupId . '%'
                ]
            )
            ->getData();
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();

        if (!empty($allowedCategory) && $categoryVisibilityStatus == 'group_wise_visibility') {
            foreach ($allowedCategory as $categoryData) {
                if ($categoryData['entity_id'] == $rootCategoryId) {
                    continue;
                }
                $allowedCategoryId[] = $categoryData['entity_id'];
            }
            $collection->addAttributeToSelect('*')
                ->joinField(
                    'category_id',
                    $this->resources->getTableName('catalog_category_product'),
                    'category_id',
                    'product_id = entity_id',
                    null,
                    'left'
                )
                ->addAttributeToFilter('category_id', $allowedCategoryId);
        }

        /*
         * To filter the cart item in quikorder
         */
        $items = $this->cart->getQuote()->getAllItems();
        $cartProduct = array();
        foreach ($items as $item) {
            $cartProduct[] = $item->getProductId();
        }

        if ($cartProduct) {
            $collection->addAttributeToFilter(
                'entity_id', [
                'nin' => $cartProduct
                ]
            );
        }

        if ($productSku) {
            $collection->addAttributeToFilter(
                'sku', [
                'like' => '%' . $productSku . '%'
                ]
            );
        }
        $collection->addAttributeToSelect('*')->load();

        if ($availableSku) {
            $collection->addAttributeToFilter(
                'sku', [
                'nin' => $skuArray
                ]
            )->setCurPage(20);
        } else {
            $collection->setCurPage(20);
        }

        return $collection;
    }
}
