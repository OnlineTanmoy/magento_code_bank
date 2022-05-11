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

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Appseconnect\B2BMage\Model\ProductFactory;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\AlreadyExistsException;

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
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Product factory
     *
     * @var ProductFactory
     */
    public $tierPriceProductFactory;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Save constructor
     *
     * @param Action\Context                             $context                 context
     * @param Session                                    $session                 session
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory         customer
     * @param ProductFactory                             $tierPriceProductFactory tier price product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager            store manager
     * @param \Magento\Indexer\Model\Processor           $processor               processor
     * @param \Magento\Indexer\Model\IndexerFactory      $indexer                 indexer
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ProductFactory $tierPriceProductFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Indexer\Model\IndexerFactory $indexer
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->tierPriceProductFactory = $tierPriceProductFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->indexer = $indexer;
        $this->processor = $processor;
    }
    
    /**
     * Save exiqute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $customer = $this->customerFactory->create()->load($data['customer_id']);
            $data['customer_name'] = $customer->getName();
            $tierPriceModel = $this->tierPriceProductFactory->create();
            try {
                $tireId=(isset($data['id']))?$data['id']:null;
                if ($tierPriceModel->isCustomerAlreadyAssigned($data['customer_id'], $tireId)) {
                    throw new AlreadyExistsException(__('Selected customer is already assigned.'));
                }
                $tierPriceModel->setData($data);
                $tierPriceModel->save();
                $this->messageManager->addSuccess(__('The tier price has been saved.'));
                $this->session->setFormData(false);
                $this->_reindex();
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit', [
                        'id' => $tierPriceModel->getId(),
                        '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the tier price.'));
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
}
