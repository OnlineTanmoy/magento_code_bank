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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Special;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\AlreadyExistsException;
use Appseconnect\B2BMage\Api\CustomerSpecialPrice\SpecialPriceRepositoryInterface;
use Appseconnect\B2BMage\Api\CustomerSpecialPrice\Data\SpecialPriceProductInterfaceFactory;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\Store;

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
     * Indexer
     *
     * @var \Magento\Indexer\Model\Indexer
     */
    public $indexer;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * Special price customer
     *
     * @var \Appseconnect\B2BMage\Model\CustomerFactory
     */
    public $specialPriceCustomerFactory;
    
    /**
     * Session
     *
     * @var Session
     */
    public $session;
    
    /**
     * Special price repository
     *
     * @var SpecialPriceRepositoryInterface
     */
    public $specialPriceRepository;
    
    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Product repository
     *
     * @var ProductRepository
     */
    public $productRepository;
    
    /**
     * Special price product interface
     *
     * @var SpecialPriceProductInterfaceFactory
     */
    public $specialPriceProductInterfaceFactory;
    
    /**
     * Save constructor
     *
     * @param ProductRepository                           $productRepository                   product repository
     * @param Session                                     $session                             session
     * @param \Magento\Customer\Model\CustomerFactory     $customerFactory                     customer model
     * @param \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCustomerFactory         special price customer
     * @param SpecialPriceRepositoryInterface             $specialPriceRepository              special price repository
     * @param SpecialPriceProductInterfaceFactory         $specialPriceProductInterfaceFactory special price product
     * @param Action\Context                              $context                             context
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager                        store manager
     * @param \Magento\Indexer\Model\Processor            $processor                           processor
     * @param \Magento\Indexer\Model\IndexerFactory       $indexer                             indexer
     */
    public function __construct(
        ProductRepository $productRepository,
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCustomerFactory,
        SpecialPriceRepositoryInterface $specialPriceRepository,
        SpecialPriceProductInterfaceFactory $specialPriceProductInterfaceFactory,
        Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Indexer\Model\IndexerFactory $indexer
    ) {
    
        parent::__construct($context);
        $this->session = $session;
        $this->specialPriceRepository = $specialPriceRepository;
        $this->specialPriceCustomerFactory = $specialPriceCustomerFactory;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->specialPriceProductInterfaceFactory = $specialPriceProductInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->indexer = $indexer;
        $this->processor = $processor;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function _getCurrentWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * Save exicution
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $customer = $this->customerFactory->create()->load($data['customer_id']);
            $data['customer_name'] = $customer->getName();
            $specialPriceModel = $this->specialPriceCustomerFactory->create();

            try {
                if ($specialPriceModel->isCustomerAlreadyAssigned($data)) {
                    throw new AlreadyExistsException(__('Selected customer and dates already exists.'));
                }
                $specialPriceModel->setData($data);
                if (isset($data['special_price_products'])) {
                    $specialPriceProducts = $data['special_price_products'];
                    $specialPriceProducts = json_decode($specialPriceProducts, true);

                    $productData = $this->_populateProductData($specialPriceProducts);
                }
                if ($data['start_date'] > $data['end_date']) {
                    $this->messageManager->addError('Please check your start or end date.');
                    
                    $id = $this->getRequest()->getParam('id');
                    
                    if ($id) {
                        return $resultRedirect->setPath(
                            '*/*/edit', [
                            'id' => $this->getRequest()
                                ->getParam('id'),
                            '_current' => true
                            ]
                        );
                    } else {
                        return $resultRedirect->setPath('*/*/new');
                    }
                }
                $specialPriceModel->save();
                $lastInsertId = $specialPriceModel->getId();
                
                if ($lastInsertId && isset($data['special_price_products'])) {
                    $this->specialPriceRepository->assignProducts($productData, $lastInsertId, true);
                }
                
                $this->messageManager->addSuccess(__('The special price has been saved.'));
                $this->session->setFormData(false);
                $this->_reindex();
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit', [
                        'id' => $specialPriceModel->getId(),
                        '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
            catch (AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the special price.')
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
     * Populate product data
     *
     * @param array $specialPriceProducts special price product
     *
     * @return array
     */
    private function _populateProductData($specialPriceProducts)
    {
        $postData = [];
        foreach ($specialPriceProducts as $key => $value) {
            $productSku = $this->productRepository->getById($key)->getSku();
            $postData['product_details'][] = [
                'product_id' => $key,
                'product_sku' => $productSku,
                'special_price' => $value
            ];
        }
        
        return $postData;
    }
}
