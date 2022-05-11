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
namespace Appseconnect\B2BMage\Block\Sales;

use Magento\Customer\Model\Session;

/**
 * Interface CustomerAccount
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerAccount extends \Magento\Framework\View\Element\Template
{
    
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    public $customerSession;

    /**
     * CustomerAccount constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param Session                                          $customerSession customer session
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Is customer login
     * 
     * @return boolean
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
