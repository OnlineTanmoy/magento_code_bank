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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Salesrep;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Appseconnect\B2BMage\Model\ResourceModel\Salesrepgrid\CollectionFactory;

/**
 * Class Edit
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Result page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Salesrep grid
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepgridFactory
     */
    public $salesRepGridFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Salesrep grid collection
     *
     * @var CollectionFactory
     */
    public $salesRepGridCollectionFactory;

    /**
     * Edit constructor.
     *
     * @param Action\Context                                  $context                       context
     * @param Session                                         $session                       session
     * @param \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesRepGridFactory           salesrep grid
     * @param CollectionFactory                               $salesRepGridCollectionFactory salesrep grid collection
     * @param \Magento\Framework\View\Result\PageFactory      $resultPageFactory             result page
     * @param \Magento\Framework\Registry                     $registry                      registry
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesRepGridFactory,
        CollectionFactory $salesRepGridCollectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->salesRepGridFactory = $salesRepGridFactory;
        $this->session = $session;
        $this->salesRepGridCollectionFactory = $salesRepGridCollectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Appseconnect_Salesrepresentative::salesrepresentative')
            ->addBreadcrumb(__('Salesrepresentative'), __('Salesrepresentative'))
            ->addBreadcrumb(__('Manage Sales Representative'), __('Manage Sales Representative'));
        return $resultPage;
    }
    
    /**
     * Edit salesrep
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $salesrepId = '';
        
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if (isset($id)) {
            $salesRepGridCollection = $this->salesRepGridCollectionFactory->create();
            $salesRepGridCollection = $salesRepGridCollection->addFieldToFilter('salesrep_customer_id', $id)->getData();
            $salesrepId = $salesRepGridCollection[0]['id'];
        }
        $this->_getSession()->unsSalesrepId();
        $this->_getSession()->setSalesrepId($salesrepId);
        
        $model = $this->salesRepGridFactory->create();
        
        if ($id) {
            $model->load($salesrepId);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This salesrep no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $data = $this->session->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_salesrepresentative', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ?
            __('Edit Sales Representative') :
            __('New Sales Representative'), $id ?
            __('Edit Sales Representative') :
            __('New Sales Representative')
        );
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Sales Representative'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(
                $model->getId() ?
                $model->getName() :
                __('New Sales Representative')
            );
        
        return $resultPage;
    }
}
