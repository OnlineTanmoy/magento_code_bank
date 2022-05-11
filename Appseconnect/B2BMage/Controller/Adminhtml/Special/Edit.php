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
use Magento\Backend\Model\Session;

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
     * Result Page
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;
    
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
     * Edit constractor
     *
     * @param Action\Context                              $context                     context
     * @param Session                                     $session                     session
     * @param \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCustomerFactory special price customer
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory           result page factory
     * @param \Magento\Framework\Registry                 $registry                    registry
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        \Appseconnect\B2BMage\Model\CustomerFactory $specialPriceCustomerFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->specialPriceCustomerFactory = $specialPriceCustomerFactory;
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
        $resultPage->setActiveMenu('Appseconnect_Pricelist::specialprice_manage')
            ->addBreadcrumb(__('Special Price'), __('Special Price'))
            ->addBreadcrumb(__('Manage Special Price'), __('Manage Special Price'));
        return $resultPage;
    }

    /**
     * Edit Blog Post
     *
     * @return                                  \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->specialPriceCustomerFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This special price no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->session->getFormData(true);
        
        if ($data) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_pricelist', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ?
            __('Edit Special Price') :
            __('New Special Price'), $id ?
            __('Edit Special Price') :
            __('New Special Price')
        );
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Special Price'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(
                $model->getId() ?
                'Edit Special Price' :
                __('New Special Price')
            );
        
        return $resultPage;
    }
}
