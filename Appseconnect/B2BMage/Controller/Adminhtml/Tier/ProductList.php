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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Tier;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class ProductList
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductList extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Tier price helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;
    
    /**
     * Product collection
     *
     * @var CollectionFactory
     */
    public $productCollectionFactory;
    
    /**
     * Result json
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Product list constructor
     *
     * @param \Magento\Framework\App\Action\Context               $context                  context
     * @param CollectionFactory                                   $productCollectionFactory product collection
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory        result json
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice          helper tier price
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CollectionFactory $productCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice
    ) {
    
        $this->helperTierprice = $helperTierprice;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $productSku = $this->getRequest()->getParam('productSku');
        
        $collection = $this->productCollectionFactory->create();
        if ($productSku) {
            $collection->addAttributeToFilter(
                'sku', [
                'like' => '%' . $productSku . '%'
                ]
            );
        }
        $collection->addAttributeToSelect('*')->load();
        $collection->setPageSize(20)->setCurPage(1);
        
        $productDetail = [];
        $output = [];
        foreach ($collection as $product) {
            $productDetail['id'] = $product->getSku();
            $productDetail['text'] = $product->getName();
            $output[] = $productDetail;
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if (empty($productDetail)) {
            return $resultJson->setData([]);
        } else {
            return $resultJson->setData($output);
        }
    }
}
