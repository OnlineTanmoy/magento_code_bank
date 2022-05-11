<?php
namespace Appseconnect\ServiceRequest\Model;

class RequestPost extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'insync_service_request';

    protected $_cacheTag = 'insync_service_request';

    protected $_eventPrefix = 'insync_service_request';

    protected function _construct()
    {
        $this->_init('Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost');
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
