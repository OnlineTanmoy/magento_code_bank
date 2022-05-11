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
     * Core Registry
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
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Product
     *
     * @var ProductFactory
     */
    public $tierPriceProductFactory;

    /**
     * Edit constructor
     *
     * @param Action\Context                             $context                 context
     * @param Session                                    $session                 session
     * @param ProductFactory                             $tierPriceProductFactory tier price product
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory       result page
     * @param \Magento\Framework\Registry                $registry                registry
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        ProductFactory $tierPriceProductFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->tierPriceProductFactory = $tierPriceProductFactory;
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
        $resultPage->setActiveMenu('Appseconnect_Pricelist::tierprice_manage')
            ->addBreadcrumb(__('Tier Price'), __('Tier Price'))
            ->addBreadcrumb(__('Manage Tier Price'), __('Manage Tier Price'));
        return $resultPage;
    }

    /**
     * Edit exiqute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->tierPriceProductFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This tier price no longer exists.'));
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
            __('Edit Tier Price') :
            __('New Tier Price'), $id ?
            __('Edit Tier Price') :
            __('New Tier Price')
        );
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Tier Price'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(
                $model->getId() ?
                __('Edit Tier Price') :
                __('New Tier Price')
            );
        
        return $resultPage;
    }
}
