<?php

namespace Appseconnect\ServiceRequest\Helper;

use Magento\Framework\App\CacheInterface;

class Search extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SERVICE_SERCH = 'ServiceSearch';


    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Search constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CacheInterface $cache
     */
    public function __construct
    (
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CacheInterface $cache
    )
    {
        $this->cache = $cache;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * to get cache search data
     * @param array $data
     */
    public function getSearchData($data = [], $update = false)
    {
        $searchData = [];
        $customerId = $this->_customerSession->getCustomerId();

        $identifier = $customerId . self::SERVICE_SERCH;
        if (empty($data)) {
            $cache = $this->cache->load($identifier);
            if ($cache) {
                $searchData = \Zend_Json::decode($cache);
            }
        }
        if ($update) {
            $searchData = $data;
            $this->cache->save(\Zend_Json::encode($data), $identifier);
        }
        return $searchData;
    }
}
