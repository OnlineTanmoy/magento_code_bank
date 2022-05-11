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
namespace Appseconnect\B2BMage\Block\ContactPerson\Contact;

use Magento\Customer\Model\Session;

/**
 * Interface Edit
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Edit extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param Session                                          $customerSession customer session
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory customer
     * @param array                                            $data            data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
    
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get contact person url
     *
     * @param $customrId customer id
     *
     * @return mixed
     */
    public function getContactPersonUrl($customrId)
    {
        return $this->getUrl(
            'b2bmage/salesrep/customer_view/', [
            'customer_id' => base64_encode($customrId['entity_id'])
            ]
        );
    }

    /**
     * Get contact person
     *
     * @param int $id customer id
     *
     * @return \Magento\Customer\Model\Customer|NULL
     */
    public function getContactPerson($id)
    {
        if ($id) {
            $model = $this->customerFactory->create()->load($id);
            return $model;
        }
        return null;
    }

}
