<?php


namespace Appseconnect\ServiceRequest\Model\Carrier;


class CarrierFactory extends \Magento\Shipping\Model\CarrierFactory
{

    /**
     * CarrierFactory constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $objectManager);
    }

    /**
     * Create carrier by its code if it is active
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Carrier\AbstractCarrier
     */
    public function createIfActive($carrierCode, $storeId = null)
    {
        if($carrierCode == 'freeshipping' && $this->checkoutSession->getStartServiceOrder() != '') {
            return $this->create(
                $carrierCode,
                $storeId
            );
        } else {
            return parent::createIfActive($carrierCode, $storeId);
        }

    }
}
