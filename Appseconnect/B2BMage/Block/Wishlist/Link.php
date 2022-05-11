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
namespace Appseconnect\B2BMage\Block\Wishlist;

use Magento\Customer\Model\Session;

/**
 * Interface Link
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    
    /**
     * Wishlist helper
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    public $wishlistHelper;
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param \Magento\Wishlist\Helper\Data                    $wishlistHelper  wishlist helper
     * @param Session                                          $customerSession customer session
     * @param array                                            $data            data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        Session $customerSession,
        array $data = []
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * To html
     *
     * @return string
     */
    public function _toHtml()
    {
        $customerType = $this->customerSession->getCustomer()->getCustomerType();
        if ($this->wishlistHelper->isAllow() && $customerType != 2) {
            $this->setTemplate("Magento_Wishlist::link.phtml");
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('wishlist');
    }

    /**
     * Get label
     * 
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('My Wish List');
    }
}
