<?php

namespace Appseconnect\MultipleDiscounts\Block\Adminhtml\Discount\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollectionFactory;

    public $_categoryCollectionFactory;

    public $_fieldFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_multiplediscount');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Discount Property')
        ]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', [
                'name' => 'id'
            ]);
        }

        $fieldset->addField('rule_name', 'text', [
            'name' => 'rule_name',
            'label' => __('Rule Name'),
            'title' => __('Rule Name'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('is_active', 'select', [
            'name' => 'is_active',
            'label' => __('Is Active'),
            'title' => __('Is Active'),
            'required' => false,
            'options' => [
                '0' => 'No',
                '1' => 'Yes'
            ],
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('website_id', 'select', [
            'name' => 'website_id',
            'label' => __('Associated to Website'),
            'title' => __('Associated to Website'),
            'required' => true,
            'options' => $this->getWebsiteId(),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('start_date', 'date', [
            'name' => 'start_date',
            'label' => __('Discount Start Date'),
            'title' => __('Discount Start Date'),
            'required' => true,
            'class' => '',
            'singleClick' => true,
            'date_format' => 'yyyy-MM-dd',
            'time' => false
        ]);

        $fieldset->addField('end_date', 'date', [
            'name' => 'end_date',
            'label' => __('Discount End Date'),
            'title' => __('Discount End Date'),
            'required' => true,
            'class' => '',
            'singleClick' => true,
            'date_format' => 'yyyy-MM-dd',
            'time' => false
        ]);

        $discountType = $fieldset->addField('discount_type', 'select', [
            'name' => 'discount_type',
            'label' => __('Discount Type'),
            'title' => __('Discount Type'),
            'required' => true,
            'options' => [
                '0' => 'Buy X item get Y item free'
//                '1' => 'Minimum price item discount'
            ],
            'disabled' => $isElementDisabled
        ]);

        $productVariation = $fieldset->addField('product_variation', 'select', [
            'name' => 'product_variation',
            'label' => __('Product Variation'),
            'title' => __('Product Variation'),
            'required' => false,
            'options' => [
                '0' => 'Same Product',
                '1' => 'Different Product'
//                '2' => 'Brands'
            ],
            'disabled' => $isElementDisabled
        ]);

        // Commented for future use
//        $brands = $fieldset->addField('brands', 'select', [
//            'name' => 'brands',
//            'label' => __('Brands'),
//            'title' => __('Brands'),
//            'required' => false,
//            'options' => $this->getBrands(),
//            'disabled' => $isElementDisabled
//        ]);

        $firstProductSku = $fieldset->addField('first_product_sku', 'text', [
            'name' => 'first_product_sku',
            'label' => __('X Sku'),
            'title' => __('X Sku'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $secondProductSku = $fieldset->addField('second_product_sku', 'text', [
            'name' => 'second_product_sku',
            'label' => __('Y Sku'),
            'title' => __('Y Sku'),
            'required' => false,
            'disabled' => $isElementDisabled
        ]);

        $firstProductQuantity = $fieldset->addField('first_product_quantity', 'text', [
            'name' => 'first_product_quantity',
            'label' => __('X Quantity'),
            'title' => __('X Quantity'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $secondProductQuantity = $fieldset->addField('second_product_quantity', 'text', [
            'name' => 'second_product_quantity',
            'label' => __('Y Quantity'),
            'title' => __('Y Quantity'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $discountTurner = $fieldset->addField('discount_turner', 'text', [
            'name' => 'discount_turner',
            'label' => __('Discount Turner'),
            'title' => __('Discount Turner'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $minimumOrderAmount = $fieldset->addField('minimum_order_amount', 'text', [
            'name' => 'minimum_order_amount',
            'label' => __('Minimum Order Amount'),
            'title' => __('Minimum Order Amount'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $minimumItemQuantity = $fieldset->addField('minimum_item_quantity', 'text', [
            'name' => 'minimum_item_quantity',
            'label' => __('Minimum Item Quantity In Cart'),
            'title' => __('Minimum Item Quantity In Cart'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $discountQuantity = $fieldset->addField('discount_quantity', 'text', [
            'name' => 'discount_quantity',
            'label' => __('Discount Quantity'),
            'title' => __('Discount Quantity'),
            'required' => false,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);

        $refFieldForXSku = $this->_fieldFactory->create(
            ['fieldData' => ['value' => '0,1', 'separator' => ','], 'fieldPrefix' => '']
        );

        $refFieldForYSku = $this->_fieldFactory->create(
            ['fieldData' => ['value' => '1', 'separator' => ','], 'fieldPrefix' => '']
        );

        // Commented for future use
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($discountType->getHtmlId(), $discountType->getName())
                ->addFieldMap($productVariation->getHtmlId(), $productVariation->getName())
                ->addFieldMap($secondProductSku->getHtmlId(), $secondProductSku->getName())
                ->addFieldMap($firstProductSku->getHtmlId(), $firstProductSku->getName())
                ->addFieldMap($firstProductQuantity->getHtmlId(), $firstProductQuantity->getName())
                ->addFieldMap($secondProductQuantity->getHtmlId(), $secondProductQuantity->getName())
                ->addFieldMap($discountTurner->getHtmlId(), $discountTurner->getName())
                ->addFieldMap($minimumOrderAmount->getHtmlId(), $minimumOrderAmount->getName())
                ->addFieldMap($minimumItemQuantity->getHtmlId(), $minimumItemQuantity->getName())
                ->addFieldMap($discountQuantity->getHtmlId(), $discountQuantity->getName())
//                ->addFieldMap($brands->getHtmlId(), $brands->getName())
                ->addFieldDependence($productVariation->getName(), $discountType->getName(), 0)
                ->addFieldDependence($firstProductSku->getName(), $discountType->getName(), 0)
                ->addFieldDependence($secondProductSku->getName(), $discountType->getName(), 0)
                ->addFieldDependence($firstProductQuantity->getName(), $discountType->getName(), 0)
                ->addFieldDependence($secondProductQuantity->getName(), $discountType->getName(), 0)
                ->addFieldDependence($discountTurner->getName(), $discountType->getName(), 0)
                ->addFieldDependence($minimumOrderAmount->getName(), $discountType->getName(), 1)
                ->addFieldDependence($minimumItemQuantity->getName(), $discountType->getName(), 1)
                ->addFieldDependence($discountQuantity->getName(), $discountType->getName(), 1)
                ->addFieldDependence($secondProductSku->getName(), $productVariation->getName(), $refFieldForYSku)
                ->addFieldDependence($firstProductSku->getName(), $productVariation->getName(), $refFieldForXSku)
//                ->addFieldDependence($brands->getName(), $discountType->getName(), 0)
//                ->addFieldDependence($brands->getName(), $productVariation->getName(), 2)
        );

        return parent::_prepareForm();
    }

    /**
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
        return __('Discount Property');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Discount Property');
    }

    /**
     *
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get Brands
     *
     * @return array
     */
    public function getBrands()
    {
        $brandsCollection = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('gt' => 2));

        $result = [];
        $result[null] = 'Please Select Brand';
        foreach ($brandsCollection as $value) {
            $result[$value->getId()] = $value->getName();
        }

        return $result;
    }
}