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
namespace Appseconnect\CompanyDivision\Controller\Adminhtml\Division;

/**
 * Class NewAction
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class NewAction extends \Magento\Backend\App\Action
{

    /**
     * Action function
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
