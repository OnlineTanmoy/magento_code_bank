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
namespace Appseconnect\B2BMage\Helper\Sales;

use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Model\Quote;

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
     * TotalsCollector
     *
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    public $totalsCollector;

    /**
     * QuoteFactory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * AddressRepositoryInterface
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * ApproverFactory
     *
     * @var \Appseconnect\B2BMage\Model\ApproverFactory
     */
    public $approverFactory;

    /**
     * OrderApproverFactory
     *
     * @var \Appseconnect\B2BMage\Model\OrderApproverFactory
     */
    public $salesOrderApproverFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context                   Context
     * @param \Magento\Quote\Model\QuoteFactory                $quoteFactory              QuoteFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository         AddressRepository
     * @param Quote\TotalsCollector                            $totalsCollector           TotalsCollector
     * @param \Appseconnect\B2BMage\Model\ApproverFactory      $approverFactory           ApproverFactory
     * @param \Appseconnect\B2BMage\Model\OrderApproverFactory $salesOrderApproverFactory SalesOrderApproverFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Appseconnect\B2BMage\Model\ApproverFactory $approverFactory,
        \Appseconnect\B2BMage\Model\OrderApproverFactory $salesOrderApproverFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->totalsCollector = $totalsCollector;
        $this->addressRepository = $addressRepository;
        $this->approverFactory = $approverFactory;
        $this->salesOrderApproverFactory = $salesOrderApproverFactory;
        parent::__construct($context);
    }

    /**
     * EstimateByAddressId
     *
     * @param string $shippingMethod ShippingMethod
     * @param int    $quoteId        QuoteId
     * @param int    $addressId      AddressId
     *
     * @return array
     */
    public function estimateByAddressId($shippingMethod, $quoteId, $addressId)
    {
        $quote = $this->quoteFactory->create()->load($quoteId);
        
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->addressRepository->getById($addressId);
        
        $data = [
            EstimateAddressInterface::KEY_COUNTRY_ID => $address->getCountryId(),
            EstimateAddressInterface::KEY_POSTCODE => $address->getPostcode(),
            EstimateAddressInterface::KEY_REGION_ID => $address->getRegionId(),
            EstimateAddressInterface::KEY_REGION => $address->getRegion()
        ];
        
        $result = $this->getShippingMethods($quote, $data);
        foreach ($result as $value) {
            if ($value['code'] == $shippingMethod) {
                return $value['price'];
            }
        }
    }

    /**
     * GetShippingMethods
     *
     * @param Quote $quote       Quote
     * @param array $addressData AddressData
     *
     * @return array
     */
    private function getShippingMethods(Quote $quote, array $addressData)
    {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->addData($addressData);
        $shippingAddress->setCollectShippingRates(true);
        
        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $output[] = $rate->getData();
            }
        }
        
        return $output;
    }

    /**
     * IsApprover
     *
     * @param int $contactPersonId ContactPersonId
     *
     * @return boolean
     */
    public function isApprover($contactPersonId)
    {
        $approverCollection = $this->approverFactory->create()
            ->getCollection()
            ->addFieldToFilter('contact_person_id', $contactPersonId);
        $approverCollection->addFieldToSelect(
            [
            'contact_person_id'
            ]
        );
        if ($approverCollection->getData()) {
            return true;
        }
        return false;
    }

    /**
     * GetApproverId
     *
     * @param int   $customerId CustomerId
     * @param float $amount     Amount
     *
     * @return NULL|int
     */
    public function getApproverId($customerId, $amount)
    {
        $amount = number_format((float) $amount, 2, '.', '');
        $approverCollection = $this->approverFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter(
                'from', [
                'lteq' => $amount
                ]
            );
        $approverCollection->getSelect()->order('main_table.from DESC');
        
        $approverData = $approverCollection->getData();
        $result = isset($approverData[0])?$approverData[0]:null;
        
        return $result;
    }

    /**
     * IsOrderApprover
     *
     * @param int $incrementId IncrementId
     * @param int $CustomerId  customerId
     *
     * @return boolean
     */
    public function isOrderApprover($incrementId, $CustomerId)
    {
        $approverCollection = $this->salesOrderApproverFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', 'On hold')
            ->addFieldToFilter('contact_person_id', $CustomerId)
            ->addFieldToFilter('increment_id', $incrementId);
        $approverData = $approverCollection->getData();
        if ($approverData) {
            return true;
        }
        return false;
    }
}
