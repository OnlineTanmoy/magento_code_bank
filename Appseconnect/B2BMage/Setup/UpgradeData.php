<?php
/**
 * Namespace
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Setup;

use Appseconnect\B2BMage\Model\ResourceModel\ContactFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\SalesSequence\Model\Builder;
use Magento\SalesSequence\Model\Config as SequenceConfig;
use Magento\TestFramework\Helper\Eav;

/**
 * Class UpgradeData
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * CustomerSetupFactory
     *
     * @var CustomerSetupFactory
     */
    public $customerSetupFactory;

    /**
     * ContactFactory
     *
     * @var ContactFactory
     */
    public $contactResourceFactory;

    /**
     * Builder
     *
     * @var Builder
     */
    public $sequenceBuilder;

    /**
     * SequenceConfig
     *
     * @var SequenceConfig
     */
    public $sequenceConfig;

    /**
     * QuotationSetupFactory
     *
     * @var QuotationSetupFactory
     */
    public $quotationSetupFactory;

    /**
     * SalesSetupFactory
     *
     * @var SalesSetupFactory
     */
    public $salesSetupFactory;

    /**
     * CategorySetupFactory
     *
     * @var CategorySetupFactory
     */
    public $categorySetupFactory;

    /**
     * Config
     *
     * @var \Magento\Eav\Model\Config
     */
    public $eavConfig;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    public $eavSetupFactory;

    /**
     * Initialize class variable
     *
     * @param CustomerSetupFactory      $customerSetupFactory   CustomerSetupFactory
     * @param ContactFactory            $contactResourceFactory ContactResourceFactory
     * @param SalesSetupFactory         $salesSetupFactory      SalesSetupFactory
     * @param CategorySetupFactory      $categorySetupFactory   CategorySetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig              EavConfig
     * @param EavSetupFactory           $eavSetupFactory        EavSetupFactory
     * @param SequenceConfig            $sequenceConfig         SequenceConfig
     * @param Builder                   $sequenceBuilder        SequenceBuilder
     * @param QuotationSetupFactory     $quotationSetupFactory  QuotationSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        ContactFactory $contactResourceFactory,
        SalesSetupFactory $salesSetupFactory,
        CategorySetupFactory $categorySetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        EavSetupFactory $eavSetupFactory,
        SequenceConfig $sequenceConfig,
        Builder $sequenceBuilder,
        QuotationSetupFactory $quotationSetupFactory
    ) {

        $this->customerSetupFactory = $customerSetupFactory;
        $this->contactResourceFactory = $contactResourceFactory;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
        $this->quotationSetupFactory = $quotationSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup   Setup
     * @param ModuleContextInterface   $context Context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $dbVersion = $context->getVersion();

        if (version_compare($context->getVersion(), '0.0.9') < 0) {
            $this->addPricelistAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.0.10') < 0) {
            $this->addStatusAttribute($setup);
            $this->addCustomerTypeAttribute($setup);
            $this->addCustomerStatusAttribute($setup);
            $this->addCreditLimitAttribute($setup);
            $this->addAvailableBalanceAttribute($setup);
            $this->updateCustomerTypeAttribute($setup);
            $this->addContactRoleAttribute($setup);
            $this->addInsyncCustomerStatusAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.0.11') < 0) {
            $this->addSpecificDiscountAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.0.12') < 0) {
            $this->addCustomerGroupAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.0.13') < 0) {
            $salesSetup = $this->salesSetupFactory->create(
                [
                    'setup' => $setup
                ]
            );

            /**
             * Add 'NEW_ATTRIBUTE' attributes for order
             */
            $options = [
                'type' => 'varchar',
                'visible' => false,
                'required' => false
            ];
            $salesSetup->addAttribute('order', 'is_placedby_salesrep', $options);
        }
        if (version_compare($context->getVersion(), '0.0.14') < 0) {
            $salesSetup = $this->salesSetupFactory->create(
                [
                    'setup' => $setup
                ]
            );

            /**
             * Add 'NEW_ATTRIBUTE' attributes for order
             */
            $options = [
                'type' => 'varchar',
                'visible' => false,
                'required' => false
            ];
            $salesSetup->addAttribute('order', 'salesrep_id', $options);
        }

        if (version_compare($context->getVersion(), '0.0.16') < 0) {
            $this->addStatusToQuotation($setup);
            $this->addQuotationEntity($setup);
        }

        if (version_compare($context->getVersion(), '0.0.19') < 0) {
            $this->updateCreditLimitAttribute($setup);
            $this->updateAvailableBalanceAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.0.25') < 0) {
            $this->updateQuotationAttribute($setup, $context);
        }

        if (version_compare($context->getVersion(), '0.0.26') < 0) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->updateAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_status',
                [
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => true
                ]);
        }
    }
    public function updateQuotationAttribute($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.25') < 0){

            $eavsetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attributecode = 'enable_quote';

            $eavsetup->addAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributecode, [
                'label' => 'Enable Quote',
                'required' => 0,
                'user_defined' => 1,
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'system' => 0,
                'position' => 100,
            ]);

            $eavsetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                $attributecode
            );

            $attribute = $this->eavConfig->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributecode);
            $attribute->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]);

            $attribute->getResource()->save($attribute);

        }


    }

    /**
     * Add Quotation Entity
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addQuotationEntity($setup)
    {
        $quotationSetup = $this->quotationSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $quotationSetup->addEntityType(
            'quotation', [
                'entity_model' =>
                    'Appseconnect\B2BMage\Model\ResourceModel\Quote',
                'attribute_model' => null,
                'table' => 'insync_customer_quote',
                'value_table_prefix' => null,
                'entity_id_field' => null,
                'increment_model' =>
                    'Magento\Eav\Model\Entity\Increment\NumericValue',
                'increment_per_store' => 1,
                'increment_pad_length' => 8,
                'increment_pad_char' => 0,
                'additional_attribute_table' => null,
                'entity_attribute_collection' => null
            ]
        );
        $this->eavConfig->clear();

        $defaultStoreIds = [
            0,
            1
        ];
        foreach ($defaultStoreIds as $storeId) {
            $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                ->setSuffix($this->sequenceConfig->get('suffix'))
                ->setStartValue($this->sequenceConfig->get('startValue'))
                ->setStoreId($storeId)
                ->setStep($this->sequenceConfig->get('step'))
                ->setWarningValue($this->sequenceConfig->get('warningValue'))
                ->setMaxValue($this->sequenceConfig->get('maxValue'))
                ->setEntityType('quotation')
                ->create();
        }

        $setup->endSetup();
    }

    /**
     * Add Status To Quotation
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addStatusToQuotation($setup)
    {
        $data = [];
        $statuses = [
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'holded' => __('On Hold'),
            'complete' => __('Complete'),
            'closed' => __('Closed'),
            'canceled' => __('Canceled'),
            'fraud' => __('Suspected Fraud'),
            'open' => __('Open'),
            'approved' => __('Approved'),
            'submitted' => __('Submitted')
        ];
        foreach ($statuses as $code => $info) {
            $data[] = [
                'status' => $code,
                'label' => $info
            ];
        }
        $setup->getConnection()->insertArray(
            $setup->getTable('insync_quotation_status'), [
            'status',
            'label'
            ], $data
        );
        $setup->endSetup();
    }

    /**
     * Add CustomerGroupAttribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addCustomerGroupAttribute($setup)
    {
        $categorySetup = $this->categorySetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'customer_group');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'customer_group', [
                'type' => 'text',
                'label' => 'Customer Group',
                'input' => 'multiselect',
                'source' =>
                    'Appseconnect\B2BMage\Model\Entity\Attribute\Source\CustomerGroups',
                'required' => false,
                'sort_order' => 100,
                'global' =>
                    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Group Information',
                'backend' =>
                    'Appseconnect\B2BMage\Model\Entity\Attribute\Backend\CustomerGroupBackend'
            ]
        );
    }

    /**
     * Add PricelistAttribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addPricelistAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "pricelist_code");

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY, "pricelist_code", [
                "type" => "varchar",
                "backend" => "",
                "label" => "Price List",
                'input' => 'select',
                'source' => 'Appseconnect\B2BMage\Model\Config\Source\PricelistOptions',
                "visible" => true,
                "required" => false,
                "default" => "",
                "frontend" => "",
                "unique" => false,
                "note" => ""

            ]
        );
        $usedInForms = [];

        $attributeCode = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'pricelist_code'
        );
        $usedInForms[] = "adminhtml_customer";
        $usedInForms[] = "customer_account_create";
        $usedInForms[] = "customer_account_edit";
        $attributeCode->setData("used_in_forms", $usedInForms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $attributeCode->save();
        $setup->endSetup();
    }

    /**
     * Add ContactRole Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addContactRoleAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'contactperson_role'
        );

        $customerSetup->addAttribute(
            'customer', 'contactperson_role', [
                'label' => 'Role',
                'default' => '0',
                'frontend' => '',
                'required' => 1,
                'source' =>
                    'Appseconnect\B2BMage\Model\Config\Source\Role',
                'visible' => 0, // <-- important, to display the attribute in customer edit
                'input' => 'select',
                'type' => 'varchar',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                "unique" => false,
                "note" => "Contact Person Role"
            ]
        );
        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute($typeId, 'contactperson_role');

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple(
                $customerSetup->getSetup()
                    ->getTable('customer_form_attribute'), [
                    'form_code' => 'adminhtml_customer',
                    'attribute_id' => $attribute['attribute_id']
                ]
            );
    }

    /**
     * Add Status Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addStatusAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            "insync_customer_status"
        );

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            "insync_customer_status",
            [
                "type" => "varchar",
                "backend" => "",
                "label" => "Status",
                'input' => 'select',
                'source' => 'Appseconnect\B2BMage\Model\Config\Source\Options',
                "visible" => true,
                "required" => false,
                "default" => "1",
                "frontend" => "",
                "unique" => false,
                "note" => "",
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => true

            ]
        );
        $usedInForms = [];

        $attributeCode = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'insync_customer_status'
        );
        $usedInForms[] = "adminhtml_customer";
        $usedInForms[] = "customer_account_create";
        $usedInForms[] = "customer_account_edit";
        $usedInForms[] = "adminhtml_checkout";
        $attributeCode->setData("used_in_forms", $usedInForms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $attributeCode->save();
    }

    /**
     * Add CustomerType Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addCustomerTypeAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );

        $customerSetup->addAttribute(
            'customer',
            UpgradeSchema::CUSTOM_ATTRIBUTE_ID,
            [
                'label' => 'Customer Type',
                'required' => 0,
                'visible' => 0, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'static',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40
            ]
        );

        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute(
            $typeId,
            UpgradeSchema::CUSTOM_ATTRIBUTE_ID
        );

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple(
                $customerSetup->getSetup()
                    ->getTable('customer_form_attribute'), [
                    'form_code' => 'adminhtml_customer',
                    'attribute_id' => $attribute['attribute_id']
                ]
            );

        $setup->endSetup();
    }

    /**
     * Add CustomerStatus Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addCustomerStatusAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            UpgradeSchema::CUSTOMER_STATUS
        );

        $customerSetup->addAttribute(
            'customer', UpgradeSchema::CUSTOMER_STATUS, [
                'label' => 'Customer Status',
                'default' => '1',
                'required' => 0,
                'visible' => 0, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'static',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => true
            ]
        );

        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute(
            $typeId,
            UpgradeSchema::CUSTOMER_STATUS
        );

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple(
                $customerSetup->getSetup()
                    ->getTable('customer_form_attribute'), [
                    'form_code' => 'adminhtml_customer',
                    'attribute_id' => $attribute['attribute_id']
                ]
            );

        $setup->endSetup();
    }

    /**
     * Add CreditLimit Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addCreditLimitAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            UpgradeSchema::CUSTOMER_CREDIT_LIMIT
        );

        $customerSetup->addAttribute(
            'customer', UpgradeSchema::CUSTOMER_CREDIT_LIMIT, [
                'label' => 'Credit Limit',
                'default' => '',
                'required' => 0,
                'visible' => 1, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'decimal',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                "note" => "This is only for B2B customer"
            ]
        );
        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute($typeId, UpgradeSchema::CUSTOMER_CREDIT_LIMIT);

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple(
                $customerSetup->getSetup()
                    ->getTable('customer_form_attribute'), [
                    'form_code' => 'adminhtml_customer',
                    'attribute_id' => $attribute['attribute_id']
                ]
            );
    }

    /**
     * Add AvailableBalance Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addAvailableBalanceAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            UpgradeSchema::CUSTOMER_AVAILABLE_BALANCE
        );

        $customerSetup->addAttribute(
            'customer', UpgradeSchema::CUSTOMER_AVAILABLE_BALANCE, [
                'label' => 'Available Balance',
                'default' => '',
                'required' => 0,
                'visible' => 1, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'decimal',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                "note" => "Don't change it"
            ]
        );
        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute(
            $typeId,
            UpgradeSchema::CUSTOMER_AVAILABLE_BALANCE
        );

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple(
                $customerSetup->getSetup()
                    ->getTable('customer_form_attribute'), [
                    'form_code' => 'adminhtml_customer',
                    'attribute_id' => $attribute['attribute_id']
                ]
            );

        $setup->endSetup();
    }

    /**
     * Update CustomerType Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function updateCustomerTypeAttribute($setup)
    {
        /**
         * Customer Type attribute
         * 1 = B2C Customer
         * 2 = Salesrepresentative
         * 3 = Contact Person
         * 4 = B2B Customer
         */
        $contactResourceModel = $this->contactResourceFactory->create();

        $data = $contactResourceModel->fetchCustomerTypeAttribute('customer_type');
        $attributeId = $data[0]['attribute_id'];

        $contactResourceModel->updateCustomerTypeAttribute('customer_type');

        $contactResourceModel->updateCustomerEavAttribute($attributeId, false);
        $data = $contactResourceModel->fetchCustomerTypeAttribute('customer_type');
        $attributeId = $data[0]['attribute_id'];

        $contactResourceModel->updateCustomerEavAttribute($attributeId, true);

        $setup->endSetup();
    }

    /**
     * Add Insync CustomerStatus Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addInsyncCustomerStatusAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            "insync_customer_status"
        );

        $contactResourceModel = $this->contactResourceFactory->create();

        $data = $contactResourceModel->fetchCustomerTypeAttribute('customer_status');
        $attributeId = $data[0]['attribute_id'];
        $contactResourceModel->updateCustomerEavAttribute($attributeId, false);
        $contactResourceModel->updateCustomerTypeAttribute('customer_status');
    }

    /**
     * Add SpecificDiscount Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addSpecificDiscountAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            "customer_specific_discount"
        );

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            "customer_specific_discount",
            [
                "type" => "varchar",
                "backend" => "",
                "label" => "Additional Discount(%)",
                'input' => 'text',
                "visible" => true,
                "required" => false,
                "frontend" => "",
                "unique" => false,
                "note" => ""

            ]
        );
        $usedInForms = [];

        $attributeCode = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'customer_specific_discount'
        );
        $usedInForms[] = "adminhtml_customer";
        $usedInForms[] = "customer_account_create";
        $usedInForms[] = "customer_account_edit";
        $attributeCode->setData("used_in_forms", $usedInForms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $attributeCode->save();
    }

    /**
     * Update CreditLimit Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function updateCreditLimitAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );


        $customerSetup->addAttribute(
            'customer', UpgradeSchema::CUSTOMER_CREDIT_LIMIT, [
                'label' => 'Credit Limit',
                'default' => '',
                'required' => 0,
                'visible' => 1, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'varchar',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                "note" => "This is only for B2B customer"
            ]
        );

        $setup->endSetup();
    }

    /**
     * Update AvailableBalance Attribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function updateAvailableBalanceAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );

        $customerSetup->addAttribute(
            'customer', UpgradeSchema::CUSTOMER_AVAILABLE_BALANCE, [
                'label' => 'Available Balance',
                'default' => '',
                'required' => 0,
                'visible' => 1, // <-- important, to display the attribute in customer edit
                'input' => 'text',
                'type' => 'varchar',
                'system' => 0, // <-- important, to have the value be saved
                'position' => 40,
                'sort_order' => 40,
                "note" => "Don't change it"
            ]
        );

        $setup->endSetup();
    }
}
