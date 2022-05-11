<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Plugin\Sales\Block\Order;

use Magento\Customer\Model\Session;

/**
 * Class History
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class History
{

    /**
     * Config
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    public $orderConfig;

    /**
     * Collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $orders;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * CollectionFactory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    public $divisionHelper;

    /**
     * Initialize class variable
     *
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data            $helperContactPerson    HelperContactPerson
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory OrderCollectionFactory
     * @param Session                                                    $customerSession        CustomerSession
     * @param \Magento\Sales\Model\Order\Config                          $orderConfig            OrderConfig
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        Session $customerSession,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Magento\Sales\Model\Order\Config $orderConfig
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderConfig = $orderConfig;
        $this->customerSession = $customerSession;
        $this->divisionHelper = $divisionHelper;
    }

    /**
     * AroundGetOrders
     *
     * @param \Magento\Sales\Block\Order\History $subject Subject
     * @param \Closure                           $proceed Proceed
     *
     * @return boolean|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function aroundGetOrders(\Magento\Sales\Block\Order\History $subject, \Closure $proceed)
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        $contactPersonRole = null;
        $salesrepId = null;
        $contactId = [];
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        if ($customerType == 2) {
            $salesrepId = $customerId;
        } elseif ($customerType == 3) {
            $contactPersonId = $this->customerSession->getCustomerId();
            $contactPersonRole = $this->customerSession->getCustomer()->getContactpersonRole();
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($contactPersonId);
            $contactId[] = $parentCustomerMapData['customer_id'];

            if ($this->divisionHelper->isParentContact($contactPersonId)) {

                $divisions = $this->divisionHelper->getChildAllDivision($parentCustomerMapData['customer_id']);

                foreach($divisions as $divisionall) {
                    if(isset($divisionall['division_id']) && $divisionall['division_id'] != '') {
                        $contactId[] = $divisionall['division_id'];
                    }
                }

            }

        }

        $proceed();
        if (!$this->orders) {
            if ($contactPersonRole == 2) {
                $orderCollection = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('contact_person_id', $contactPersonId);
            } elseif ($salesrepId) {
                $orderCollection = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('salesrep_id', $salesrepId);
            } elseif ($contactId) {
                $orderCollection = $this->orderCollectionFactory->create()
                    ->addFieldToFilter(
                        'customer_id', [
                            'in' => $contactId
                        ]
                    ) ;
            } else {
                $orderCollection = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $customerId);
            }
            $this->orders = $orderCollection->addFieldToSelect('*')
                ->addFieldToFilter(
                    'status', [
                        'in' => $this->orderConfig->getVisibleOnFrontStatuses()
                    ]
                )
                ->setOrder('created_at', 'desc');
        }

        return $this->orders;
    }
}
