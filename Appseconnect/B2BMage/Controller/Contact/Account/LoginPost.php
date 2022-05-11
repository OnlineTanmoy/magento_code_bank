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
namespace Appseconnect\B2BMage\Controller\Contact\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class LoginPost
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class LoginPost extends \Magento\Customer\Controller\Account\LoginPost
{
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Login post
     *
     * @param Context                                         $context                   context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson       contact person helper
     * @param Session                                         $customerSession           customer session
     * @param AccountManagementInterface                      $customerAccountManagement customer account management
     * @param CustomerUrl                                     $customerHelperData        customer helper
     * @param Validator                                       $formKeyValidator          form key
     * @param AccountRedirect                                 $accountRedirect           account redirect
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect
    ) {
        $this->helperContactPerson = $helperContactPerson;
        parent::__construct(
            $context,
            $customerSession,
            $customerAccountManagement,
            $customerHelperData,
            $formKeyValidator,
            $accountRedirect
        );
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->session->isLoggedIn() || ! $this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (! empty($login['username']) && ! empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $status = null;
                    $status = $this->helperContactPerson->isValidCustomer($customer->getId());
                    $this->_validateAccess($customer, $status);
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed.' .
                                ' <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Invalid login or password.'));
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }
        }
        
        return $this->accountRedirect->getRedirect();
    }

    /**
     * Access validation
     *
     * @param AccountManagementInterface $customer account manager
     * @param string                     $status   status
     *
     * @return void
     */
    private function _validateAccess($customer, $status)
    {
        if ($status === 'B2BCustomer') {
            $this->messageManager->addError(__('Please login with Contact Person or Sales Representative.'));
        } elseif ($status === 'inactive') {
            $this->messageManager->addError(__('Customer is inactive.'));
        } elseif ($status === 'customerInactive') {
            $this->messageManager->addError(__('Contact Person is inactive.'));
        } elseif ($status === 'salesrepInactive') {
            $this->messageManager->addError(__('Sales Representative is inactive.'));
        } else {
            $this->session->setCustomerDataAsLoggedIn($customer);
            $this->session->regenerateId();
        }
    }
}
