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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerTierPrice\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory as PriceCollectionFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

/**
 * Abstract Class Main
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Main extends Generic implements TabInterface
{

    /**
     * System store
     *
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * Status
     *
     * @var \Appseconnect\B2BMage\Model\Status
     */
    public $status;

    /**
     * Pricelist collection
     *
     * @var PriceCollectionFactory
     */
    public $pricelistFactory;

    /**
     * Customer collection
     *
     * @var CustomerCollectionFactory
     */
    public $customerCollectionFactory;

    /**
     * Website collection
     *
     * @var WebsiteCollectionFactory
     */
    public $websiteCollectionFactory;

    /**
     * Product colleceteion
     *
     * @var ProductCollectionFactory
     */
    public $customerTierPriceFactory;

    /**
     * Helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helper;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context             $context                   context
     * @param \Magento\Framework\Registry                         $registry                  registry
     * @param \Magento\Framework\Data\FormFactory                 $formFactory               form
     * @param \Magento\Store\Model\System\Store                   $systemStore               system store
     * @param PriceCollectionFactory                              $pricelistFactory          price list
     * @param CustomerCollectionFactory                           $customerCollectionFactory customer collection
     * @param WebsiteCollectionFactory                            $websiteCollectionFactory  website
     * @param ProductCollectionFactory                            $customerTierPriceFactory  customer tier price
     * @param \Appseconnect\B2BMage\Model\Status                  $status                    status
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helper                    helper
     * @param array                                               $data                      data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        PriceCollectionFactory $pricelistFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        WebsiteCollectionFactory $websiteCollectionFactory,
        ProductCollectionFactory $customerTierPriceFactory,
        \Appseconnect\B2BMage\Model\Status $status,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helper,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->pricelistFactory = $pricelistFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->customerTierPriceFactory = $customerTierPriceFactory;
        $this->status = $status;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_pricelist');
        
        $isElementDisabled = false;

        $form = $this->_formFactory->create();
        
        $form->setHtmlIdPrefix('page_');
        
        $fieldset = $form->addFieldset(
            'base_fieldset', [
            'legend' => __('Customer Information')
            ]
        );
        
        if ($model->getId()) {
            $fieldset->addField(
                'id', 'hidden', [
                'name' => 'id'
                ]
            );
        }

        $afterElementHtml = '<p class="nm"><small>' . ' Change pricelist from customer account section ' . '</small></p>';

        $fieldset->addField(
            'website_id', 'select', [
            'name' => 'website_id',
            'label' => __('Associated to Website'),
            'title' => __('Associated to Website'),
            'required' => true,
            'options' => $this->getWebsiteId(),
            'disabled' => $isElementDisabled
            ]
        );
        
        $customer = $fieldset->addField(
            'customer_id', 'select', [
            'name' => 'customer_id',
            'label' => __('B2B Customer'),
            'title' => __('B2B Customer'),
            'required' => true,
            'options' => $this->getCustomerId()
            ]
        );

        $fieldset->addField(
            'pricelist_name', 'text', [
                'after_element_html' => $afterElementHtml,
                'name' => 'pricelist_name',
                'label' => __('Pricelist'),
                'title' => __('Pricelist'),
                'required' => false,
                'readonly' => true,
                'style' => 'border:0; padding-left: 0;'
            ]
        );

        $fieldset->addField(
            'pricelist_id', 'hidden', [
                'name' => 'pricelist_id',
                'label' => __('Pricelist ID'),
                'title' => __('Pricelist ID'),
                'required' => false
            ]
        );
        
        $fieldset->addField(
            'discount_type', 'select', [
            'name' => 'discount_type',
            'label' => __('Discount Type'),
            'title' => __('Discount Type'),
            'required' => false,
            'options' => [
                '0' => 'By Fixed Price',
                '1' => 'By Percentage'
            ],
            'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'is_active', 'select', [
            'name' => 'is_active',
            'label' => __('Is Active'),
            'title' => __('Is Active'),
            'required' => false,
            'options' => [
                '0' => 'No',
                '1' => 'Yes'
            ],
            'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'minimum_order_amount', 'text', [
                'name' => 'minimum_order_amount',
                'label' => __('Minimum Order Amount'),
                'title' => __('Minimum Order Amount'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'class' => 'validate-number'
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    /**
     * Get customer id
     *
     * @return array
     */
    public function getCustomerId()
    {
        $customerCollection = $this->customerCollectionFactory->create()
            ->addNameToSelect()
            ->addFieldToFilter('customer_type', 4);
        $customerData = $customerCollection->getData();
        $result = [];
        $result[null] = 'Please Select Customer';
        foreach ($customerData as $value) {
            $result[$value['entity_id']] = $value['name'];
        }
        
        return $result;
    }

    /**
     * Get website id
     *
     * @return array
     */
    public function getWebsiteId()
    {
        $website = $this->websiteCollectionFactory->create();
        $output = $website->getData();
        $result=[];
        foreach ($output as $val) {
            $result[$val["website_id"]] = $val['name'];
        }
        
        return $result;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Customer Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Customer Information');
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId resource id
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
