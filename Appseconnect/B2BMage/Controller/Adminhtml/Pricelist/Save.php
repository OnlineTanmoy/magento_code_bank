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

namespace Appseconnect\B2BMage\Controller\Adminhtml\Pricelist;

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterfaceFactory;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductRepository;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;

/**
 * Class Save
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * Processor
     *
     * @var \Magento\Indexer\Model\Processor
     */
    public $processor;

    /**
     * Pricelist price
     *
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * Pricelist collection
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * Product Repository
     *
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * Pricelist Repository
     *
     * @var \Appseconnect\B2BMage\Model\PricelistRepository
     */
    public $pricelistRepository;

    /**
     * Product assign interface
     *
     * @var ProductAssignInterfaceFactory
     */
    public $productAssignInterfaceFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Indexer
     *
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    public $indexer;

    /**
     * Save constructor.
     *
     * @param ProductRepository                               $productRepository             product repository
     * @param \Appseconnect\B2BMage\Model\PriceFactory        $pricelistPriceFactory         pricelist price
     * @param Session                                         $session                       session
     * @param \Appseconnect\B2BMage\Model\PricelistRepository $pricelistRepository           pricelist repository
     * @param ProductAssignInterfaceFactory                   $productAssignInterfaceFactory product assign interface
     * @param Action\Context                                  $context                       context
     * @param CollectionFactory                               $collectionFactory             pricelist collection
     * @param \Magento\Indexer\Model\Processor                $processor                     processor
     * @param \Magento\Indexer\Model\IndexerFactory           $indexer                       indexer
     */
    public function __construct(
        ProductRepository $productRepository,
        \Appseconnect\B2BMage\Model\PriceFactory $pricelistPriceFactory,
        Session $session,
        \Appseconnect\B2BMage\Model\PricelistRepository $pricelistRepository,
        ProductAssignInterfaceFactory $productAssignInterfaceFactory,
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Indexer\Model\IndexerFactory $indexer
    ) {

        $this->pricelistPriceFactory = $pricelistPriceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->pricelistRepository = $pricelistRepository;
        $this->productAssignInterfaceFactory = $productAssignInterfaceFactory;
        parent::__construct($context);
        $this->indexer = $indexer;
        $this->processor = $processor;
    }

    /**
     * Save Pricelist.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $productElements = $this->_populateProductData($data);

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $pricelistModel = $this->pricelistPriceFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $pricelistModel->load($id);
            }
            $pricelistModel->setData($data);
            $pricelistModel->save();
            try {
                $lastInsertId = $pricelistModel->getId();

                if ($lastInsertId && isset($data['pricelist_products'])) {
                    $this->_linkProducts($lastInsertId, $productElements);
                }
                $this->messageManager->addSuccess(__('The pricelist has been saved.'));
                $this->session->setFormData(false);
                $this->_reindex();
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit', [
                            'id' => $pricelistModel->getId(),
                            '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the pricelist.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit', [
                    'id' => $this->getRequest()
                        ->getParam('id')
                ]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Reindex
     *
     * @return void
     */
    private function _reindex()
    {
        $indexer = $this->indexer->create();
        $indexer->load('catalogrule_rule');
        $indexer->reindexAll();
    }

    /**
     * Linked products
     *
     * @param $lastInsertId last inserted id
     * @param $productData  product data
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _linkProducts($lastInsertId, $productData)
    {
        $productAssignInterfaceModel = $this->productAssignInterfaceFactory->create();
        $productAssignInterfaceModel->setPricelistId($lastInsertId);
        $productAssignInterfaceModel->setProductData($productData);
        $this->pricelistRepository->assignProducts($productAssignInterfaceModel, true);
    }

    /**
     * Populate product data
     *
     * @param array $data data
     *
     * @return array
     */
    private function _populateProductData($data)
    {
        $productData = [];
        $pricelistProducts = [];
        if (isset($data['pricelist_products'])) {
            $pricelistProducts = $data['pricelist_products'];
            $pricelistProducts = json_decode($pricelistProducts, true);

            foreach ($pricelistProducts as $key => $value) {
                $productSku = $this->productRepository->getById($key)->getSku();
                $allValue = explode('__', $value);
                if (!$allValue[1]) {
                    $product = $this->productRepository->getById($key);
                    if ($data['id']) {
                        $allValue[0] = $product->getFinalPrice() * $data['discount_factor'];
                    }
                }
                $productData[] = [
                    'sku' => $productSku,
                    'price' => $allValue[0],
                    'is_manual' => $allValue[1],
                ];
            }
        }
        return $productData;
    }
}
