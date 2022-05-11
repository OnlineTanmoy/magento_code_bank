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
namespace Appseconnect\B2BMage\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class SalesrepProcessor
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class SalesrepProcessor
{
    /**
     * Account manager
     *
     * @var \Magento\Customer\Model\AccountManagement
     */
    public $accountManager;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Salesrep helper
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $helperSalesrep;
    
    /**
     * Salesrep grid
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepgridFactory
     */
    public $salesrepGridFactory;
    
    /**
     * Salesrep model
     *
     * @var \Appseconnect\B2BMage\Model\SalesrepFactory
     */
    public $salesrepModelFactory;
    
    /**
     * Salesrep collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory
     */
    public $salesrepCollectionFactory;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Resource
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resources;

    /**
     * SalesrepProcessor constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory           customer
     * @param \Magento\Customer\Model\AccountManagement       $accountManager            account manager
     * @param SalesrepgridFactory                             $salesrepGridFactory       salesrep grid
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data      $helperSalesrep            salesrep helper
     * @param SalesrepFactory                                 $salesrepModelFactory      salesrep model
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson       contact person helper
     * @param ResourceModel\Salesrep\CollectionFactory        $salesrepCollectionFactory salesrep collection
     * @param \Magento\Framework\App\ResourceConnection       $resources                 resource
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AccountManagement $accountManager,
        \Appseconnect\B2BMage\Model\SalesrepgridFactory $salesrepGridFactory,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $helperSalesrep,
        \Appseconnect\B2BMage\Model\SalesrepFactory $salesrepModelFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory $salesrepCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resources
    ) {
    
        $this->accountManager = $accountManager;
        $this->customerFactory = $customerFactory;
        $this->helperSalesrep = $helperSalesrep;
        $this->salesrepGridFactory = $salesrepGridFactory;
        $this->salesrepModelFactory = $salesrepModelFactory;
        $this->salesrepCollectionFactory = $salesrepCollectionFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->resources = $resources;
    }

    /**
     * Check customer id
     *
     * @param int $customerId customer id
     *
     * @return int
     */
    public function checkCustomerId($customerId)
    {
        $id = null;
        $id = $this->customerFactory->create()
            ->load($customerId)
            ->getId();
        return $id;
    }
    /**
     * Proccess
     *
     * @param array $requestDataArray request data array
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return array
     */
    public function process($requestDataArray)
    {
        $salesrepModel = $this->salesrepModelFactory->create();
        $count = 0;
        $countCustomerId = count($requestDataArray['customer_ids']);
        foreach ($requestDataArray['customer_ids'] as $customerId) {
            $returnData = [];
            $data['salesrep_id'] = $requestDataArray['salesrep_id'];
            $data['customer_id'] = $customerId;
            if (!$this->checkCustomerId($customerId)) {
                $returnData['error'] = "Customer ID[" . $customerId . "] doesn't exist ";
                $count ++;
                if ($count == $countCustomerId) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Customer IDs doesn't exist")
                    );
                }
            }
            $isB2BCustomer = $this->helperContactPerson->isB2Bcustomer($customerId);
            if (! $isB2BCustomer) {
                $returnData['error'] = "Not a valid customer [" . $customerId . "]";
                $count ++;
                if ($count == $countCustomerId) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Not a valid customer [" . $customerId . "]")
                    );
                }
            } elseif ($this->salesrepCollectionFactory->create()            ->addFieldToFilter('salesrep_id', $data['salesrep_id'])            ->addFieldToFilter('customer_id', $data['customer_id'])            ->getData()
            ) {
                $returnData['error'] = "Customer ID[" . $customerId . "] is assigned to [" . $data['salesrep_id'] . "]";
                $count ++;
                if ($count == $countCustomerId) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Customer IDs are already associated to [" . $data['salesrep_id'] . "]")
                    );
                }
            } elseif ($this->salesrepCollectionFactory->create()            ->addFieldToFilter('customer_id', $customerId)            ->getData()
            ) {
                $returnData['error'] = "Customer ID[" . $customerId . "] is already associated to other salesrep";
                $count ++;
                if ($count == $countCustomerId) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __("Customer IDs are associated to other salesrep")
                    );
                }
            } else {
                $returnData = $this->setSalesRepData($data);
                $returnData['salesrep_id'] = $requestDataArray['salesrep_id'];
            }
            $return[] = $returnData;
        }
        return $return;
    }
    
    /**
     * Set sales rep data
     * 
     * @param array $data data
     *
     * @return mixed
     */
    public function setSalesRepData($data)
    {
        $salesrepModel = $this->salesrepModelFactory->create();
        $salesrepModel->setData($data)->save();
        $result = $salesrepModel->load($salesrepModel->getId())
            ->getData();
        return $result;
    }
}
