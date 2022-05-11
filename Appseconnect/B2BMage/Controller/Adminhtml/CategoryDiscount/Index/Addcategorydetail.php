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
namespace Appseconnect\B2BMage\Controller\Adminhtml\CategoryDiscount\Index;

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\CategorydiscountFactory;

/**
 * Class Addcategorydetail
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Addcategorydetail extends \Magento\Backend\App\Action
{

    /**
     * Index proccesor module
     *
     * @var \Magento\Indexer\Model\Processor
     */
    public $processor;

    /**
     * Indexer Module
     *
     * @var \Magento\Indexer\Model\Indexer
     */
    public $indexer;

    /**
     * Category discount factory model
     *
     * @var CategorydiscountFactory
     */
    public $categoryDiscountFactory;

    /**
     * Resource index factory model
     *
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    public $resources;

    /**
     * Contractor
     *
     * @param Action\Context                            $context                 contaxt
     * @param CategorydiscountFactory                   $categoryDiscountFactory category discount factory
     * @param \Magento\Framework\App\ResourceConnection $resources               resource connection
     * @param \Magento\Indexer\Model\Processor          $processor               indexer processores
     * @param \Magento\Indexer\Model\IndexerFactory     $indexer                 indexer module factory
     */
    public function __construct(
        Action\Context $context,
        CategorydiscountFactory $categoryDiscountFactory,
        \Magento\Framework\App\ResourceConnection $resources,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Indexer\Model\IndexerFactory $indexer
    ) {
    
        parent::__construct($context);
        $this->categoryDiscountFactory = $categoryDiscountFactory;
        $this->resources = $resources;
        $this->indexer = $indexer;
        $this->processor = $processor;
    }

    /**
     * Exiqute function
     *
     * @return void
     */
    public function execute()
    {
        $data = [];
        $data = $this->_request->getParams();
        $connection = $this->resources->getConnection();
        $variable = $data['action'];
        switch ($variable) {
        case 'delete':
            $tablename = $this->resources->getTableName('insync_categorydiscount');
            $where['categorydiscount_id =?'] = $data['cod'];
            $where['customer_id =?'] = $data['cus'];
            $resultData = $connection->delete($tablename, $where);
            break;
            
        case 'process':
            $tablename = $this->resources->getTableName('insync_categorydiscount');
            if (isset($data['data']['CatagoryDetailUpdate'])) {
                $updateData = $data['data']['CatagoryDetailUpdate'];
                foreach ($updateData as $val) {
                    if ($val['discountfactor_up'] != '' && $val['category_id_up'] != '') {
                        $insertVal = [];
                        $where = [];
                        $insertVal['discount_factor'] = $val['discountfactor_up'];
                        $insertVal['category_id'] = $val['category_id_up'];
                        $insertVal['is_active'] = $val['status_up'];
                        $insertVal['discount_type'] = $val['discounttype_up'];
                        $insertVal['categorydiscount_id'] = $val['categorydiscount_id'];
                        $this->_putData($insertVal);
                    }
                }
                $this->_reindex();
            }
            if (isset($data['data']['CatagoryDetail'])) {
                $dataSend = $data['data']['CatagoryDetail'];
                foreach ($dataSend as $val) {
                    if ($val['discount_factor'] != '' && $val['category_id'] != '') {
                        $val['customer_id'] = $data['cus'];
                        $this->_postData($val);
                    }
                }
                $this->_reindex();
            }
            break;
        }
    }

    /**
     * Reindex all
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
     * Post Data
     *
     * @param array $data data array
     *
     * @return void
     */
    private function _postData($data)
    {
        $model = $this->categoryDiscountFactory->create();
        $model->setData($data);
        $model->save();
    }
    
    /**
     * Put Data
     *
     * @param array $data data array
     *
     * @return void
     */
    private function _putData($data)
    {
        if ($data['categorydiscount_id']) {
            $model = $this->categoryDiscountFactory->create();
            $model->setData($data);
            $model->save();
        }
    }
}
