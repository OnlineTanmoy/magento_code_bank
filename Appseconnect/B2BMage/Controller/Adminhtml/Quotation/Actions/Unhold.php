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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Actions;

use Magento\Sales\Controller\OrderInterface;

/**
 * Class Unhold
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Unhold extends \Appseconnect\B2BMage\Controller\Quotation\AbstractController\Unhold
{

    /**
     * Is allowed
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return true;
    }
}
