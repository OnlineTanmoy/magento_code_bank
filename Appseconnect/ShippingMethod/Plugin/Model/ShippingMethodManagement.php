<?php

namespace Appseconnect\ShippingMethod\Plugin\Model;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Appseconnect\ShippingMethod\Model\ResourceModel\ShippingMethod\CollectionFactory;

class ShippingMethodManagement
{
    protected $cart;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var CollectionFactory
     */
    public $shippingMethodCollectionFactory;

    public function __construct
    (
        Cart $cart,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        CollectionFactory $shippingMethodCollectionFactory
    ) {
        $this->cart = $cart;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->shippingMethodCollectionFactory = $shippingMethodCollectionFactory;
    }

    public function afterEstimateByAddressId($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    private function filterOutput($output)
    {
        $grandTotal = $this->cart->getQuote()->getGrandTotal();

        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $customerDetail['customer_id'];
        }

        $shippingMethodCollection = $this->shippingMethodCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', 1)
            ->getData();

        $minimumOrderAmount = null;
        if (isset($shippingMethodCollection[0])) {
            $minimumOrderAmount = $shippingMethodCollection[0]['minimum_order_value'];
        }

        $free = [];
        foreach ($output as $shippingMethod) {
            if ($shippingMethod->getCarrierCode() == 'freeshipping' && $shippingMethod->getMethodCode() == 'freeshipping') {
                if (isset($minimumOrderAmount) && $grandTotal < $minimumOrderAmount) {
                    continue;
                }
            }
            $free[] = $shippingMethod;
        }

        if ($free) {
            return $free;
        }

        return $output;
    }
}