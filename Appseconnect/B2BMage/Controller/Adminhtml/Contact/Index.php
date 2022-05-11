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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Contact;

/**
 * Class Index
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Index extends \Magento\Customer\Controller\Adminhtml\Index\Index
{

    /**
     * Action function
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($customerId) {
            return $resultRedirect->setPath('customer/index/edit/id/' . $customerId);
        } else {
            return $resultRedirect->setPath('*/*/');
        }
    }
}
