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
 * Class Hold
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Hold extends \Appseconnect\B2BMage\Controller\Quotation\AbstractController\Hold
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
