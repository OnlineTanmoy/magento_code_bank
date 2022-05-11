<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Layout;

use Magento\Customer\Model\Session;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\PageCache\Model\DepersonalizeChecker;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\FormKey;

/**
 * Class DepersonalizePlugin
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DepersonalizePlugin
{
    /**
     * DepersonalizeChecker
     *
     * @var DepersonalizeChecker
     */
    public $depersonalizeChecker;

    /**
     * SessionManagerInterface
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    public $session;

    /**
     * Session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Visitor
     *
     * @var \Magento\Customer\Model\Visitor
     */
    public $visitor;

    /**
     * Int
     *
     * @var int
     */
    public $customerGroupId;

    /**
     * String
     *
     * @var string
     */
    public $formKey;

    /**
     * DepersonalizePlugin constructor.
     *
     * @param DepersonalizeChecker                    $depersonalizeChecker DepersonalizeChecker
     * @param SessionManagerInterface                 $session              Session
     * @param Session                                 $customerSession      CustomerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory      CustomerFactory
     * @param \Magento\Customer\Model\Visitor         $visitor              Visitor
     */
    public function __construct(
        DepersonalizeChecker $depersonalizeChecker,
        SessionManagerInterface $session,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Visitor $visitor
    ) {
        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->visitor = $visitor;
        $this->depersonalizeChecker = $depersonalizeChecker;
    }

    /**
     * Before generate Xml
     *
     * @param \Magento\Framework\View\LayoutInterface $subject Subject
     *
     * @return array
     */
    public function beforeGenerateXml(\Magento\Framework\View\LayoutInterface $subject)
    {
        $check = false;
        $check = $this->depersonalizeChecker->checkIfDepersonalize($subject);
        if ($this->depersonalizeChecker->checkIfDepersonalize($subject)) {
            $this->customerGroupId = $this->customerSession->getCustomerGroupId();
            $this->formKey = $this->session->getData(\Magento\Framework\Data\Form\FormKey::FORM_KEY);
        }
        return [];
    }

    /**
     * Change sensitive customer data if the depersonalization is needed.
     *
     * @param LayoutInterface $subject subject
     * 
     * @return void
     */
    public function afterGenerateElements(LayoutInterface $subject)
    {
        if ($this->depersonalizeChecker->checkIfDepersonalize($subject)) {
            $this->visitor->setSkipRequestLogging(true);
            $this->visitor->unsetData();
            $this->session->clearStorage();
            $this->customerSession->clearStorage();
            $this->session->setData(FormKey::FORM_KEY, $this->formKey);
            $this->customerSession->setCustomerGroupId($this->customerGroupId);
            $this->customerSession->setCustomer($this->customerFactory->create()->setGroupId($this->customerGroupId));
        }
    }
}
