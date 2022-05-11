<?php
namespace Appseconnect\B2BMage\Block\Customer\Form;

use Magento\Customer\Model\Session;

class Register extends \Magento\Directory\Block\Data
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Register constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data);
    }

    /**
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::registration.phtml");

        return parent::_toHtml();
    }

    /**
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * @return static
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/savepost', []);
    }
}
