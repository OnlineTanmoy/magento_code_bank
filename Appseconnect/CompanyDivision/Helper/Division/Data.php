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

namespace Appseconnect\CompanyDivision\Helper\Division;

use Appseconnect\CompanyDivision\Model\ResourceModel\Division\CollectionFactory;

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

    const TYPE_CONTACT_PERSON = 3;

    const TYPE_SALES_REP = 2;

    const TYPE_B2B = 4;

    const TYPE_B2C = 1;

    /**
     * DivisionFactory
     *
     * @var \Appseconnect\CompanyDivision\Model\DivisionFactory
     */
    public $divisionFactory;

    /**
     * CollectionFactory
     *
     * @var \Appseconnect\CompanyDivision\Model\ResourceModel\Division\CollectionFactory
     */
    public $divisionCollectionFactory;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * HttpContext
     *
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    public $divisionId = array();

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context Context
     * @param \Appseconnect\B2BMage\Model\ContactFactory $contactPersonFactory ContactPersonFactory
     * @param CollectionFactory $contactPersonAddressCollectionFactory ContactPersonAddressCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory CustomerFactory
     * @param \Magento\Framework\App\Http\Context $httpContext HttpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Appseconnect\CompanyDivision\Model\DivisionFactory $divisionFactory,
        CollectionFactory $divisionCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper,
        \Magento\Framework\Data\Collection $collection
    )
    {
        $this->divisionFactory = $divisionFactory;
        $this->divisionCollectionFactory = $divisionCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->httpContext = $httpContext;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->collection = $collection;
        parent::__construct($context);
    }

    /**
     * IsAdministrator
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return number|int
     */
    public function isParentContact($contactPersonId)
    {
        $contact = $this->customerFactory->create()->load($contactPersonId);
        // echo $contactPersonId; echo $this->contactPersonHelper->contactPersonExist($contactPersonId);exit;
        if ($this->contactPersonHelper->isContactPerson($contact)) {
            $customerData = $this->contactPersonHelper->getCustomerId($contactPersonId);
            return $this->isMainCustomer($customerData['customer_id']);
        } else {
            return false;
        }
    }

    public function isMainCustomer($customerId)
    {
        $division = $this->divisionCollectionFactory->create()
            ->addFieldToFilter('division_id', $customerId)
            ->getFirstItem();

        if (!empty($division->getData())) {
            return false;
        } else {
            return true;
        }
    }

    public function getChildAllDivision($customerId, $flag = false)
    {

        $divisions = $this->divisionCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('id', 'ASC');

        $count = 0;
        if ($flag) {
            $divisionall = $this->customerFactory->create()
                ->load($customerId);

            $varienObject = new \Magento\Framework\DataObject();

            $row['id'] = 0;
            $row['name'] = $divisionall->getName();
            $row['email'] = $divisionall->getEmail();
            $row['customer_id'] = 0;
            $row['division_id'] = $divisionall->getId();
            $row['status'] = $divisionall->getIsActive();

            $varienObject->setData($row);
            $this->collection->addItem($varienObject);
        }
        foreach ($divisions as $division) {

            $childDivisions = $this->divisionCollectionFactory->create()
                ->addFieldToFilter('customer_id', $division->getDivisionId());
            if (count($childDivisions) > 0) {
                $this->getChildAllDivision($division->getDivisionId());
            }
            if (!in_array($division->getId(), $this->divisionId)) {
                $varienObject = new \Magento\Framework\DataObject();

                $this->divisionId[] = $division->getId();
                $row['id'] = $division->getId();
                $row['name'] = $division->getName();
                $row['email'] = $division->getEmail();
                $row['customer_id'] = $division->getCustomerId();
                $row['division_id'] = $division->getDivisionId();
                $row['status'] = $division->getIsActive();

                $varienObject->setData($row);
                $this->collection->addItem($varienObject);
            }
        }

        return $this->collection;
    }

    public function divisionByCustomerId($customerId)
    {
        $division = $this->divisionCollectionFactory->create()
            ->addFieldToFilter('division_id', $customerId)
            ->getFirstItem();

        return $division;
    }

    public function getMainCustomerId($customerId)
    {

        $divisions = $this->divisionFactory->create()->getCollection()
            ->addFieldToFilter('division_id', $customerId)
            ->getData();

        if (!$divisions) {
            return $customerId;
        } else {
            foreach ($divisions as $division) {
                $customerId = $this->getMainCustomerId($division['customer_id']);
                break;
            }
            return $customerId;
        }
    }

    public function getParentCustomerId($customerId)
    {

        $divisions = $this->divisionFactory->create()->getCollection()
            ->addFieldToFilter('division_id', $customerId)
            ->getFirstItem();


        return $divisions->getCustomerId();
    }

    public function createDivision($customer, $parentCompanyId)
    {

        $divisionModel = $this->divisionFactory->create();
        $divisionData = array();
        $divisionData['customer_id'] = $parentCompanyId;
        $divisionData['division_id'] = $customer->getId();
        $divisionData['is_active'] = $customer->getIsActive();
        $divisionData['name'] = $customer->getName();
        $divisionData['email'] = $customer->getEmail();

        $divisionModel->setData($divisionData);
        $divisionModel->save();

        return $divisionModel->getId();
    }
}
