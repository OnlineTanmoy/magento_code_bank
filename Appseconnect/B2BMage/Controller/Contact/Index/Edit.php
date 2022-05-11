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
namespace Appseconnect\B2BMage\Controller\Contact\Index;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;

/**
 * Class Edit
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * Result page
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Edit constractor
     *
     * @param Context                                         $context             context
     * @param Session                                         $customerSession     customer session
     * @param PageFactory                                     $resultPageFactory   result page
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
    ) {
    
        $this->customerSession = $customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerSessionId = $this->customerSession->getCustomerId();
        $isAdministrator = $this->helperContactPerson->isAdministrator($customerSessionId);
        if ($isAdministrator != 1) {
            return $this->resultRedirectFactory->create()->setPath('customer/account');
        }
        if (! ($customerSessionId)) {
            $this->messageManager->addError(__('Access Denied.'));
            $resultRedirect->setPath('');
            return $resultRedirect;
        }
        $customerData = $this->helperContactPerson->getCustomerId($this->customerSession->getCustomerId());
        $customerId = $customerData['customer_id'];
        $customerDataa = $this->helperContactPerson->getCustomerId(
            $this->getRequest()
                ->getParam('id')
        );
        if ($customerDataa != '') {
            if (isset($customerDataa['customer_id'])) {
                $customerIda = $customerDataa['customer_id'];
                if ($customerId != $customerIda) {
                    $this->messageManager->addError(__('Invalid ID was provided in the url'));
                    return $resultRedirect->setPath('*/*/index_listing');
                }
            } else {
                $this->messageManager->addError(__('Invalid ID was provided in the url'));
                return $resultRedirect->setPath('*/*/index_listing');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()
            ->set(__('Edit Contact Person'));
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2bmage/contact/index_listing');
        }
        return $resultPage;
    }
}
