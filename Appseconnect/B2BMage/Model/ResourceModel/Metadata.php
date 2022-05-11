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
namespace Appseconnect\B2BMage\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;

/**
 * Class Metadata
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Metadata
{

    /**
     * String
     *
     * @var string
     */
    public $resourceClassName;

    /**
     * String
     *
     * @var string
     */
    public $modelClassName;

    /**
     * Metadata constructor.
     *
     * @param $resourceClassName ResourceClassName
     * @param $modelClassName    ModelClassName
     */
    public function __construct($resourceClassName, $modelClassName)
    {
        $this->resourceClassName = $resourceClassName;
        $this->modelClassName = $modelClassName;
    }

    /**
     * GetMapper
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function getMapper()
    {
        return ObjectManager::getInstance()->get($this->resourceClassName);
    }

    /**
     * GetNewInstance
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface
     */
    public function getNewInstance()
    {
        return ObjectManager::getInstance()->create($this->modelClassName);
    }
}
