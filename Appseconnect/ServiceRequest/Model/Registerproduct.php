<?php
namespace Appseconnect\ServiceRequest\Model;

class Registerproduct extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'appseconnect_registerproduct';

    protected $_cacheTag = 'appseconnect_registerproduct';

    protected $_eventPrefix = 'appseconnect_registerproduct';

    protected function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\ResourceModel\Registerproduct');
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
