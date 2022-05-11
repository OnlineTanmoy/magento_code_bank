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
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

use Magento\Sales\Controller\OrderInterface;

/**
 * Class Submit
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Submit extends \Appseconnect\B2BMage\Controller\Quotation\AbstractController\Submit
{
    /**
     * Is allowed
     *
     * @return boolean
     */
    private function _isAllowed()
    {
        return true;
    }
}
