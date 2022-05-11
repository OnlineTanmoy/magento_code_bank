<?php

namespace Appseconnect\MultipleDiscounts\Controller\Adminhtml\Price;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Store\Model\Store;

class Save extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Magento\Indexer\Model\Processor
     */
    public $processor;

    /**
     *
     * @var \Magento\Indexer\Model\Indexer
     */
    public $indexer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Appseconnect\MultipleDiscounts\Model\DiscountFactory
     */
    public $multipleDiscountFactory;
    
    /**
     * @var Session
     */
    public $session;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    protected $productCollectionFactory;
    
    /**
     * @param Session $session
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory
     * @param Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Indexer\Model\Processor $processor
     * @param \Magento\Indexer\Model\IndexerFactory $indexer
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\MultipleDiscounts\Model\DiscountFactory $multipleDiscountFactory,
        Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Indexer\Model\IndexerFactory $indexer,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
    
        parent::__construct($context);
        $this->session = $session;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->indexer = $indexer;
        $this->processor = $processor;
        $this->multipleDiscountFactory = $multipleDiscountFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function getCurrentWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $multipleDiscountFactoryModel = $this->multipleDiscountFactory->create();

            $firstProductCollection = null;
            $secondProductCollection = null;

            if (isset($data['first_product_sku'])) {
                $firstProductCollection = $this->productCollectionFactory->create()
                    ->addFieldToFilter('sku', $data['first_product_sku'])
                    ->getData();
            }

            if (isset($data['second_product_sku'])) {
                $secondProductCollection = $this->productCollectionFactory->create()
                    ->addFieldToFilter('sku', $data['second_product_sku'])
                    ->getData();
            }
            try {
                $multipleDiscountFactoryModel->setData($data);
                if ($data['start_date'] > $data['end_date']) {
                    $this->messageManager->addError('Please check your start or end date.');
                    
                    $id = $this->getRequest()->getParam('id');
                    
                    if ($id) {
                        return $resultRedirect->setPath('*/*/edit', [
                            'id' => $this->getRequest()
                                ->getParam('id'),
                            '_current' => true
                        ]);
                    } else {
                        return $resultRedirect->setPath('*/*/new');
                    }
                } elseif (!empty($data['first_product_sku']) && !$firstProductCollection) {
                    $this->messageManager->addError('Product with X Sku does not exist.');

                    $id = $this->getRequest()->getParam('id');

                    if ($id) {
                        return $resultRedirect->setPath('*/*/edit', [
                            'id' => $this->getRequest()
                                ->getParam('id'),
                            '_current' => true
                        ]);
                    } else {
                        return $resultRedirect->setPath('*/*/new');
                    }
                } elseif (!empty($data['second_product_sku']) && !$secondProductCollection) {
                    $this->messageManager->addError('Product with Y Sku does not exist.');

                    $id = $this->getRequest()->getParam('id');

                    if ($id) {
                        return $resultRedirect->setPath('*/*/edit', [
                            'id' => $this->getRequest()
                                ->getParam('id'),
                            '_current' => true
                        ]);
                    } else {
                        return $resultRedirect->setPath('*/*/new');
                    }
                }

                $multipleDiscountFactoryModel->save();
                $lastInsertId = $multipleDiscountFactoryModel->getId();

                $this->messageManager->addSuccess(__('The discount has been saved.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'id' => $multipleDiscountFactoryModel->getId(),
                        '_current' => true
                    ]);
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
                    __('Something went wrong while saving the discount.')
                );
            }
            
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()
                    ->getParam('id')
            ]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
