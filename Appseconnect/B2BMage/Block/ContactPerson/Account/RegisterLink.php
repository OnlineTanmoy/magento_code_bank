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
namespace Appseconnect\B2BMage\Block\ContactPerson\Account;

use Magento\Customer\Model\Context;

/**
 * Interface RegisterLink
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class RegisterLink extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    private $_httpContext;

    /**
     * Registration
     *
     * @var \Magento\Customer\Model\Registration
     */
    private $_registration;

    /**
     * Customer Url
     *
     * @var \Magento\Customer\Model\Url
     */
    private $_customerUrl;
    
    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * RegisterLink constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context   $context       context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig  scope config
     * @param \Magento\Framework\App\Http\Context                $_httpContext  http context
     * @param \Magento\Customer\Model\Registration               $_registration registration
     * @param \Magento\Customer\Model\Url                        $_customerUrl  customer url
     * @param array                                              $data          data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
        \Magento\Framework\App\Http\Context $_httpContext,
        \Magento\Customer\Model\Registration $_registration,
        \Magento\Customer\Model\Url $_customerUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $_scopeConfig;
        $this->_httpContext = $_httpContext;
        $this->_registration = $_registration;
        $this->_customerUrl = $_customerUrl;
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $canRegister = $this->_scopeConfig->getValue('insync_account/create/type', 'store');
        if (! $canRegister) {
            return '';
        }
        if (! $this->_registration->isAllowed() || $this->_httpContext->getValue(Context::CONTEXT_AUTH)) {
            return '';
        }
        return parent::_toHtml();
    }

}
