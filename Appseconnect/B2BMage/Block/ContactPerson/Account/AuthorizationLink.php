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
 * Interface AuthorizationLink
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $customerUrl, $postDataHelper, $data);
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;

    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _tohtml()
    {
        if ($this->customerSession->getCustomerId()){
            $this->httpContext->setValue(Context::CONTEXT_AUTH,true,false);

        }
        $this->setTemplate("Appseconnect_B2BMage::contactperson/account/link/authorization.phtml");

        return parent::_toHtml();
    }

}
