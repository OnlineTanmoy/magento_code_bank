<?php
/**
 * Namespace
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

/**
 * Class Categorydiscount
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Categorydiscount extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\Categorydiscount');
    }
}
