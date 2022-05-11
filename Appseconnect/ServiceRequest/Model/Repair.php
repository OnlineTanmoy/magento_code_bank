<?php
namespace Appseconnect\ServiceRequest\Model;

class Repair extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'appseconnect_repair';

    protected $_cacheTag = 'appseconnect_repair';

    protected $_eventPrefix = 'appseconnect_repair';

    protected function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\ResourceModel\Repair');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
