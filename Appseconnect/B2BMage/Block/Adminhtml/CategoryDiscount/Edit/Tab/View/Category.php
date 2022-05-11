<?php
/**
 * Namespace
 *
 * @category Block/Adminhtml/CategoryDiscount/Edit/Tab/view
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\CategoryDiscount\Edit\Tab\View;

/**
 * Class Category
 *
 * @category Block/Adminhtml/CategoryDiscount/Edit/Tab/view
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Category extends Group\AbstractGroup
{

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    public function getInitialCustomerGroups()
    {
        return [
            $this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')
        ];
    }

    /**
     * Prepare HTML
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::categorydiscount/form.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Get Customer specific category
     *
     * @param int $customerId Customer Id
     *
     * @return array
     */
    public function getcustomercategory($customerId = null)
    {
        $categories = $this->categoryDiscountCollectionFactory->create();
        $categories->addFieldToFilter('customer_id', $customerId);
        return $categories->getData();
    }

    /**
     * Get Category name
     *
     * @param int $customerId Customer ID
     * @param int $categoryId Category ID
     *
     * @return array
     */
    public function getCategoryNames($customerId = null, $categoryId = null)
    {
        $categoryData = [];
        if ($customerId) {
            $categoryValues = $this->categoryDiscountCollectionFactory->create()
                ->addFieldToSelect('category_id')
                ->addFieldToFilter('customer_id', $customerId);
            $categoryData = [];
            foreach ($categoryValues as $data) {
                if ($categoryId != $data['category_id']) {
                    $categoryData[] = $data['category_id'];
                }
            }
        }
        $categories = $this->categoryCollectionFactory->create()->addAttributeToSelect('*');
        if ($customerId && ! empty($categoryData)) {
            $categories->addAttributeToFilter(
                'entity_id', [
                'nin' => $categoryData
                ]
            );
        }
        $result = [];
        foreach ($categories as $category) :
            if ($category->getId() && $category->getId() != 1) {
                $result[$category->getId()] = $category->getName();
            }
        endforeach
        ;
        return $result;
    }

    /**
     * Override prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Add Category Discount Price'),
                'class' => 'add-more-cat-disc'
                ]
            );
        $button->setName('add_tier_price_item_button');
        
        $this->setChild('add_button', $button);
        
        $submit_button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Save'),
                'class' => 'category add'
                ]
            );
        $submit_button->setName('submit_button');
        
        $this->setChild('submit_button', $submit_button);
        return parent::_prepareLayout();
    }
    
    /**
     * Return Base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
