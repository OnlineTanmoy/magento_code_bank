<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Block\Adminhtml\Sales\Approver;

/**
 * Class Listing
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Listing extends Group\AbstractGroup
{

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    public function _getInitialCustomerGroups()
    {
        return [
            $this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')
        ];
    }

    /**
     * Tohtml
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::sales/approver/listing.phtml");

        return parent::_toHtml();
    }

    /**
     * GetContactpersonList
     *
     * @param int     $customerId CustomerId
     * @param boolean $type       Type
     *
     * @return array
     */
    public function getContactpersonList($customerId = null, $type = false)
    {
        $approverResourceModel = $this->approverResourceFactory->create();
        $customerCollection = $this->customerFactory->create()->getCollection();
        $collection = $approverResourceModel->getContacts($customerId, $customerCollection);
        $collection->addExpressionAttributeToSelect(
            'name',
            '(CONCAT({{firstname}},"  ",{{lastname}}))',
            [
                'firstname',
                'lastname'
            ]
        );

        if ($type) {
            $contactPersonIds = $this->getApprover($customerId);
            $idList = [];
            if ($contactPersonIds) {
                foreach ($contactPersonIds as $val) {
                    $idList[] = $val['contact_person_id'];
                }
                $collection->addAttributeToFilter(
                    'entity_id',
                    [
                        'nin' => $idList
                    ]
                );
            }
        }

        return $collection->getData();
    }

    /**
     * GetApprover
     *
     * @param int $customerId CustomerId
     *
     * @return array
     */
    public function getApprover($customerId)
    {
        $approverData = $this->approverCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);
        return $approverData->getData();
    }
}
