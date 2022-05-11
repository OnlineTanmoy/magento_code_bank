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
use Appseconnect\B2BMage\Model\PriceFactory;
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
     * Core Registry
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
     * Pricelist price
     *
     * @var PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Edit constructor.
     *
     * @param Action\Context                             $context               context
     * @param PriceFactory                               $pricelistPriceFactory pricelist price
     * @param Session                                    $session               session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory     result page
     * @param \Magento\Framework\Registry                $registry              registry
     */
    public function __construct(
        Action\Context $context,
        PriceFactory $pricelistPriceFactory,
        Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->pricelistPriceFactory = $pricelistPriceFactory;
        $this->session = $session;
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
        $resultPage->setActiveMenu('Appseconnect_Pricelist::pricelist')
            ->addBreadcrumb(__('Pricelist'), __('Pricelist'))
            ->addBreadcrumb(__('Manage Pricelist'), __('Manage Pricelist'));
        return $resultPage;
    }

    /**
     * Edit pricelist
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->pricelistPriceFactory->create();
        
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->messageManager->addError(__('This post no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $data = $this->session->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        $this->coreRegistry->register('insync_pricelist', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ?
            __('Edit Pricelist') :
            __('New Pricelist'), $id ?
            __('Edit Pricelist') :
            __('New Pricelist')
        );
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Pricelist'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(
                $model->getId() ?
                $model->getPricelistName() :
                __('New Pricelist')
            );
        
        return $resultPage;
    }
}
