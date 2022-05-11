<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\ContactPerson\Form\Login;

/**
 * Interface Info
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Info extends \Magento\Customer\Block\Form\Login\Info
{
    
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public $scopeConfig;

    /**
     * Info constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context   $context      context
     * @param \Magento\Customer\Model\Registration               $registration registration
     * @param \Magento\Customer\Model\Url                        $customerUrl  customer url
     * @param \Magento\Checkout\Helper\Data                      $checkoutData checkout data
     * @param \Magento\Framework\Url\Helper\Data                 $coreUrl      core url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig  scope config
     * @param array                                              $data         data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registration,
            $customerUrl,
            $checkoutData,
            $coreUrl,
            $data
        );
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * To html
     *
     * @return string|NULL
     */
    public function _toHtml()
    {
        $canRegister = $this->scopeConfig->getValue('insync_account/create/type', 'store');
        if (! $canRegister) {
            return '';
        }
        return parent::_toHtml();
    }

}
