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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerTierPrice\Edit\Tab\View;

/**
 * Abstract Class TierPrice
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class TierPrice extends Group\AbstractGroup
{

    /**
     * Get customer tier price
     *
     * @param $tierPriceId tier price id
     *
     * @return mixed
     */
    public function getCustomerTierPrice($tierPriceId)
    {
        $tierPriceCollection = $this->tierPriceCollectionFactory->create();
        $tierPriceCollection->addFieldToFilter('parent_id', $tierPriceId);
        return $tierPriceCollection->getData();
    }

    /**
     * Tohtml
     *
     * @return string
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_B2BMage::customertierprice/product/price/tier_prices.phtml");
        
        return parent::_toHtml();
    }

    /**
     * Get product sku
     *
     * @return $product
     */
    public function getProductSku()
    {
        $product = $this->tierPriceHelper->getAllProduct();
        return $product;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Add Product Tier Price'),
                'class' => 'add'
                ]
            );
        $button->setName('add_product_tier_price_item_button');
        
        $this->setChild('add_button', $button);
        
        $submit_button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                'label' => __('Save'),
                'class' => 'submit'
                ]
            );
        $submit_button->setName('submit_button');
        
        $this->setChild('submit_button', $submit_button);
        return parent::_prepareLayout();
    }
    
    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
