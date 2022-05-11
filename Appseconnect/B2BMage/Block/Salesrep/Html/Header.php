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
namespace Appseconnect\B2BMage\Block\Salesrep\Html;

use Magento\Catalog\Model\Session;

/**
 * Interface View
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Header extends \Magento\Theme\Block\Html\Header
{
    
    /**
     * Customer session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Header constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context        context
     * @param Session                                          $catalogSession catalog session
     * @param array                                            $data           data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $catalogSession,
        array $data = []
    ) {
    
        $this->catalogSession = $catalogSession;
        parent::__construct($context, $data);
    }

    /**
     * Tohtml
     *
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::salesrep/html/header.phtml");
        
        return parent::_toHtml();
    }
    
    /**
     * Get message
     *
     * @return Session
     */
    public function getMessage()
    {
        return $this->catalogSession->getSalesrepMessage();
    }
}
