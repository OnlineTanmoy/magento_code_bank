<?php

namespace Appseconnect\B2BMage\Block\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Context;


/**
 * @api
 * @since 100.0.2
 */
class Customer extends \Magento\Customer\Block\Account\Customer
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_viewHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context              $httpContext,
        \Magento\Customer\Model\Session                  $customerSession,
        array                                            $data = []
    )
    {
        parent::__construct( $context, $httpContext, $data );
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        if ((bool)$this->httpContext->getValue( \Magento\Customer\Model\Context::CONTEXT_AUTH )) {

            return (bool)$this->httpContext->getValue( \Magento\Customer\Model\Context::CONTEXT_AUTH );
        } else if ($this->customerSession->getCustomerId()) {
            return true;
        }
    }
}
