<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Observer\ContactPerson;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddressUpdateObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AddressUpdateObserver implements ObserverInterface
{

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;
    
    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * AddressUpdateObserver constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory     $customerFactory CustomerFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date            Date
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->customerFactory = $customerFactory;
        $this->date = $date;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return \Appseconnect\B2BMage\Observer\ContactPerson\AddressUpdateObserver|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $address = $observer->getCustomerAddress();
        if (! $address->hasDataChanges()) {
            return $this;
        }
        $customerData = $this->customerFactory->create()->load($address->getCustomerId());
        $customerData->setUpdatedAt($this->date->gmtDate());
        $customerData->save();
    }
}
