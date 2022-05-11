<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Quote;

use Magento\Framework\App\ObjectManager;

/**
 * Class TotalFactory
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class TotalFactory
{

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName InstanceName
     * @param array  $data         Data
     *
     * @return \Appseconnect\B2BMage\Model\Quote\Total
     */
    public function create($instanceName, array $data = [])
    {
        return ObjectManager::getInstance()->create($instanceName, $data);
    }
}
