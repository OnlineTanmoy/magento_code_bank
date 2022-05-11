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

namespace Appseconnect\B2BMage\Controller\Adminhtml\Mobiletheme;

use Magento\Backend\App\Action;
use Appseconnect\B2BMage\Model\MobilethemeFactory;
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
     * Result page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Mobile Theme Factory
     *
     * @var MobilethemeFactory
     */
    public $mobilethemeFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * System config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Edit constructor.
     *
     * @param Action\Context                                     $context            context
     * @param MobilethemeFactory                                 $mobilethemeFactory mobile theme object
     * @param Session                                            $session            session object
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory  result page
     * @param \Magento\Framework\Registry                        $registry           registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig        system config
     */
    public function __construct(
        Action\Context $context,
        MobilethemeFactory $mobilethemeFactory,
        Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->mobilethemeFactory = $mobilethemeFactory;
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->scopeConfig = $scopeConfig;
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
        $resultPage->setActiveMenu('Appseconnect_B2BMage::mobile_design_theme')
            ->addBreadcrumb(__('Mobile Theme'), __('Mobile Theme'))
            ->addBreadcrumb(__('Manage Mobile Theme'), __('Manage Mobile Theme'));
        return $resultPage;
    }

    /**
     * Edit mobile theme data
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->mobilethemeFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This post no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $apiUrl = $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope);

        if ($apiUrl == '' || !$apiUrl) {
            $this->messageManager->addError(__('Please set the mobile theme api in sytem cofiguration.'));
        }

        $this->messageManager->addNotice(__('Please Generate the Registration key after submit the mandetory field. And send this key to you customer for mobile app installation.'));

        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('insync_mobile_theme', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ?
            __('Mobile Theme') :
            __('Mobile Theme'), $id ?
            __('Mobile Theme') :
            __('Mobile Theme')
        );
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Mobile Theme'));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(
                $model->getId() ?
                $model->getOrganisationName() :
                __('Mobile Theme')
            );

        return $resultPage;
    }
}
