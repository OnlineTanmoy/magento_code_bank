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
namespace Appseconnect\B2BMage\Block\Adminhtml\CustomerSpecialPrice\Special\Edit\Tab;

/**
 * Abstract Class Main
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * System store
     *
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * Customer Collection
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    public $customerCollectionFactory;

    /**
     * Website collection
     *
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollectionFactory;

    /**
     * Price collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistFactory;

    /**
     * Helper
     *
     * @var \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data
     */
    public $helper;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                           $context                   context
     * @param \Magento\Framework\Registry                                       $registry                  registry
     * @param \Magento\Framework\Data\FormFactory                               $formFactory               form
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory  $customerCollectionFactory customer collection
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory      $websiteCollectionFactory  website collection
     * @param \Magento\Store\Model\System\Store                                 $systemStore               system store
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistFactory          price list
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data            $helper                    helper
     * @param array                                                             $data                      data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistFactory,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helper,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->pricelistFactory = $pricelistFactory;
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

        /**
         * Is element disable
         */
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
        
        $fieldset->addField(
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
            'start_date', 'date', [
            'name' => 'start_date',
            'label' => __('Special Price Start Date'),
            'title' => __('Special Price Start Date'),
            'required' => true,
            'class' => '',
            'singleClick' => true,
            'date_format' => 'yyyy-MM-dd',
            'time' => false
            ]
        );
        
        $fieldset->addField(
            'end_date', 'date', [
            'name' => 'end_date',
            'label' => __('Special Price End Date'),
            'title' => __('Special Price End Date'),
            'required' => true,
            'class' => '',
            'singleClick' => true,
            'date_format' => 'yyyy-MM-dd',
            'time' => false
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
     * @param string $resourceId resiurce id
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
