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
use Appseconnect\B2BMage\Model\ResourceModel\Contact\CollectionFactory as ContactCollectionFactory;

/**
 * Interface Listing
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Listing extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * Customer
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    private $_customers;

    /**
     * Contact person collection
     *
     * @var ContactCollectionFactory
     */
    public $contactCollectionFactory;

    /**
     * Helper contact person
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Listing constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context                  context
     * @param ContactCollectionFactory                         $contactCollectionFactory contact person collection
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data  $helperContactPerson      contact person helper
     * @param Session                                          $_customerSession         customer session
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory          customer model
     * @param array                                            $data                     data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ContactCollectionFactory $contactCollectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        Session $_customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->contactCollectionFactory = $contactCollectionFactory;
        $this->_customerSession = $_customerSession;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );
        $pager->setLimit(10)
            ->setShowAmounts(true);
        if ($this->getContactPersons()) {
            $pager->setCollection($this->getContactPersons());
            $this->setChild('pager', $pager);
            $this->getContactPersons()->getData();
        }


        
        return $this;
    }

    /**
     * Get Contect persons
     *
     * @return boolean|\Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getContactPersons()
    {
        if (!($contactPersonId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        $customerData = $this->helperContactPerson->getCustomerId($contactPersonId);
        $customerId = $customerData['customer_id'];
        if (!$this->_customers) {
            $this->_customers = $this->customerFactory->create()->getCollection();
            $contactFactory = $this->contactCollectionFactory->create();
            $contactCollection = $contactFactory->addFieldToSelect('contactperson_id')
                ->addFieldToFilter('customer_id', $customerId);
            $contactIds = [];
            foreach ($contactCollection as $data) {
                if ($data['contactperson_id'] != $contactPersonId) {
                    $contactIds[] = $data['contactperson_id'];
                }
            }
            $this->_customers->addExpressionAttributeToSelect(
                'name', '(CONCAT({{firstname}},"  ",{{lastname}}))', [
                    'entity_id',
                    'firstname',
                    'lastname',
                    'customer_status',
                    'contactperson_role'
                ]
            )->addFieldToFilter(
                'entity_id', [
                    'in' => $contactIds
                ]
            );
        }
        return $this->_customers;
    }

    /**
     * Get contact person add url
     *
     * @return string
     */
    public function getContactPersonAddUrl()
    {
        return $this->getUrl('b2bmage/contact/index_add/');
    }

    /**
     * Get contact person edit url
     *
     * @param int $customrId customer id
     *
     * @return string
     */
    public function getContactPersonEditUrl($customrId)
    {
        return $this->getUrl(
            'b2bmage/salesrep/customer_view/', [
                'customer_id' => base64_encode($customrId['entity_id'])
            ]
        );
    }

    /**
     * Get page html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

}
