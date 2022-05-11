<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Helper\Salesrep;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $websiteCollectionFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * SalesrepgridFactory
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepgridFactory
     */
    public $salesrepGridFactory;

    /**
     * SalesrepFactory
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesrepFactory;

    /**
     * Session
     *
     * @var \Magento\Catalog\Model\Session
     */
    public $catalogSession;

    /**
     * Data constructor.
     *
     * @param CollectionFactory                               $websiteCollectionFactory WebsiteCollectionFactory
     * @param Session                                         $customerSession          CustomerSession
     * @param \Appseconnect\B2BMage\Model\SalesrepFactory     $salesrepFactory          SalesrepFactory
     * @param \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesrepGridFactory      SalesrepGridFactory
     */
    public function __construct(
        CollectionFactory $websiteCollectionFactory,
        Session $customerSession,
        \Appseconnect\B2BMage\Model\SalesrepFactory $salesrepFactory,
        \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesrepGridFactory
    ) {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->customerSession = $customerSession;
        $this->salesrepGridFactory = $salesrepGridFactory;
        $this->salesrepFactory = $salesrepFactory;
    }

    /**
     * GetWebsite
     *
     * @return array
     */
    public function getWebsite()
    {
        $customer = $this->websiteCollectionFactory->create();
        $output = $customer->getData();
        $result = [];

        foreach ($output as $val) {
            $result[$val['website_id']] = $val['name'];
        }

        return $result;
    }

    /**
     * GetCustomerId
     *
     * @param int     $salesrepId SalesrepId
     * @param boolean $reset      Reset
     *
     * @return array
     */
    public function getCustomerId($salesrepId, $reset)
    {
        $salesrepCollection = $this->salesrepFactory->create()->getCollection();

        $output = $salesrepCollection->getData();
        $result = [];

        foreach ($output as $val) {
            if ($reset && $salesrepId != $val['salesrep_id']) {
                $result[] = $val['customer_id'];
            } elseif (!$reset && $salesrepId == $val['salesrep_id']) {
                $result[] = $val['customer_id'];
            }
        }

        return $result;
    }

    /**
     * GetSalesrepId
     *
     * @param int $customerId CustomerId
     *
     * @return array
     */
    public function getSalesrepId($customerId)
    {
        $salesrepCollection = $this->salesrepFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $salesrepCollection->addFieldToSelect(
            [
                'salesrep_id',
                'id'
            ]
        );
        return $salesrepCollection->getData();
    }

    /**
     * IsSalesrep
     *
     * @param int     $customerId CustomerId
     * @param boolean $getData    GetData
     *
     * @return boolean|array
     */
    public function isSalesrep($customerId, $getData = false)
    {
        $return = false;
        $salesrepCollection = $this->salesrepGridFactory->create()
            ->getCollection()
            ->addFieldToFilter('salesrep_customer_id', $customerId);
        $salesrepCollection->addFieldToSelect(
            [
                'id',
                'website_id'
            ]
        );
        $salesrepData = $salesrepCollection->getData();
        if ($salesrepData && $getData) {
            $return = $salesrepData;
        } elseif ($salesrepData) {
            $return = true;
        }
        return $return;
    }

    /**
     * IsAllow
     *
     * @return boolean
     */
    public function isAllow()
    {
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        return $customerType != 2 ? true : false;
    }

    /**
     * GetType
     *
     * @return int
     */
    public function getType()
    {
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        return $customerType;
    }

    /**
     * GetSalesrepIdFromSession
     *
     * @return int
     */
    public function getSalesrepIdFromSession()
    {
        return $this->getCatalogSession()->getSalesrepId();
    }

    /**
     * GetCatalogSession
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->create(\Magento\Catalog\Model\Session::class);
        return $this->catalogSession;
    }

    /**
     * GetSalesrepCustomerId
     *
     * @param $salesrepGridId SalesrepGridId
     *
     * @return mixed
     */
    public function getSalesrepCustomerId($salesrepGridId)
    {
        $salesrepCollection = $this->salesrepGridFactory->create()
            ->getCollection()
            ->addFieldToFilter('id', $salesrepGridId);
        $salesrepCollection->addFieldToSelect('salesrep_customer_id');

        foreach ($salesrepCollection as $salesrep) {
            return $salesrep->getSalesrepCustomerId();
        }
    }
}
