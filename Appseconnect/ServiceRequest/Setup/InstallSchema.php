<?php

namespace Appseconnect\ServiceRequest\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Service Request Table
        if (!$installer->tableExists('insync_service_request')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('insync_service_request')
            )
                ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Entity ID'
                )
                ->addColumn('ra_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    80, ['nullable' => true], 'Service Increment ID'
                )
                ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['unsigned' => true, 'nullable' => false,], 'Customer Id'
                )
                ->addColumn('model_number', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => false], 'Product SKU'
                )
                ->addColumn('product_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true], 'Product Description'
                )
                ->addColumn('serial_number', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => false], 'Serial Number'
                )
                ->addColumn('copack_serial_number', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => false], 'Copack Serial Number'
                )
                ->addColumn('short_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true], 'Short Descriptionion'
                )
                ->addColumn('detailed_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true], 'Detailed Description'
                )
                ->addColumn('safety1', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true], 'Safety 1'
                )
                ->addColumn('safety2', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true], 'Safety 2'
                )
                ->addColumn('safety3', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true], 'Safety 3'
                )
                ->addColumn('terms_condition', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    5, ['nullable' => false], 'Terms Condition'
                )
                ->addColumn('device_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50, ['nullable' => true], 'Device Type'
                )
                ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => false, 'default' => 1], 'Post Status'
                )
                ->addColumn('post', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At'
                )
                ->addColumn('file_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true, 'default' => ''], 'Receipt File'
                )
                ->addColumn('purchase_order_number', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true, 'default' => ''], 'Purchase Order Number'
                )
                ->addColumn('purchase_order_file', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true, 'default' => ''], 'Purchase Order File'
                )
                ->addColumn('is_warranty', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Is Warranty'
                )
                ->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Order Id'
                )
                ->addColumn('customer_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255, ['nullable' => true, 'default' => ''], 'Customer Name'
                )
                ->addColumn('draft_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => true], 'Draft date'
                )
                ->addColumn('submit_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => true], 'Submit date'
                )
                ->addColumn('transit_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => true], 'Transit date'
                )
                ->addColumn('service_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => true], 'Service date'
                )
                ->addColumn('complete_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null, ['nullable' => true], 'Complete date'
                )
                ->addColumn('shipping_address_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Default shipping address id'
                )
                ->addColumn('billing_address_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Default billing address id'
                )
                ->addColumn('fpr_price', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    null, ['nullable' => true, 'default' => 0], 'FPR Price'
                )
                ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Store id'
                )
                ->addColumn('contact_person_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Contact Person ID'
                )
                ->addColumn('service_quote_required', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null, ['nullable' => true, 'default' => 0], 'Service Quotation required ?'
                )
                ->setComment('Service Request Table');
            $installer->getConnection()->createTable($table);
        }

        // Service status table
        $tableName = $setup->getTable('insync_service_status');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create insync_service_status table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'status Id')
                ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 200,
                    ['nullable' => true, 'default' => ''], 'Label')
                ->setComment('insync_service_status')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);

            $data = array(
                array('id' => NULL, 'label' => 'Draft'),
                array('id' => NULL, 'label' => 'Submitted'),
                array('id' => NULL, 'label' => 'In-transit'),
                array('id' => NULL, 'label' => 'In-service'),
                array('id' => NULL, 'label' => 'On Hold'),
                array('id' => NULL, 'label' => 'Not Repairable'),
                array('id' => NULL, 'label' => 'Waiting for Parts'),
                array('id' => NULL, 'label' => 'Approval Required'),
                array('id' => NULL, 'label' => 'Completed'),
                array('id' => NULL, 'label' => 'Closed without Repair'),
                array('id' => NULL, 'label' => 'Cancel'),
            );
            $setup->getConnection()->insertArray($tableName, array('id', 'label'), $data);
        }

        // Warranty table
        $tableName = $setup->getTable('insync_product_warranty');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create insync_contactperson table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'Warranty Id')
                ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['nullable' => true], 'Customer Id')
                ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['nullable' => false, 'default' => 0], 'Warrenty Active')
                ->addColumn('contactperson_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['nullable' => true], 'Contactperson Id')
                ->addColumn('mfr_serial_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 200,
                    ['nullable' => true, 'default' => ''], 'Mfr Serial Number')
                ->addColumn('copack_serial_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 200,
                    ['nullable' => true, 'default' => ''], 'Co-pack Serial Number')
                ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 200,
                    ['nullable' => true, 'default' => ''], 'SKU')
                ->addColumn('warranty_start_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null,
                    ['nullable' => true], 'Warranty Start Date')
                ->addColumn('warranty_end_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null,
                    ['nullable' => true], 'Warranty End Date')
                ->addColumn('product_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255,
                    ['nullable' => true, 'default' => ''], 'Product Name')
                ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 11,
                    ['nullable' => true, 'default' => ''], 'Status')
                ->addColumn('contract_status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 11,
                    ['nullable' => true, 'default' => ''], 'Contract Status')
                ->addColumn('equipment_card_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50,
                    ['nullable' => true, 'default' => ''], 'Equipment Card No')
                ->addColumn('equipment_creation_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null,
                    ['nullable' => true], 'Equipment Creation Date')
                ->addColumn('contract_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50,
                    ['nullable' => true, 'default' => ''], 'Contract Number')
                ->addColumn('contract_creation_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null,
                    ['nullable' => true], 'Contract Creation Date')
                ->addColumn('termination_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null,
                    ['nullable' => true], 'Termination Date')
                ->addColumn('customer_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255,
                    ['nullable' => true, 'default' => ''], 'Customer Name')
                ->setComment('insync_product_warranty')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        // Fix price repair table
        $tableName = $setup->getTable('insync_fixed_repaired');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create insync_fixed_repaired table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'Fixed Repair Id')
                ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 200,
                    ['nullable' => true, 'default' => ''], 'SKU')
                ->addColumn('repair_cost', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 14,
                    ['nullable' => true, 'default' => null], 'Repair Cost')
                ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->addColumn('product_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255,
                    ['nullable' => true, 'default' => null], 'Product Description')
                ->setComment('insync_fixed_repaired')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }


        // insert Service and Draft numbering in config table
        $data = ['scope' => 'default', 'scope_id' => 0, 'path' => 'insync/service/lastdraft', 'value' => '0'];
        $setup->getConnection()->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
        $data = ['scope' => 'default', 'scope_id' => 0, 'path' => 'insync/service/lastservice', 'value' => '0'];
        $setup->getConnection()->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);


        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'), 'service_id',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 11, 'nullable' => true, 'comment' => 'Service Id']
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'), 'service_number',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 50, 'nullable' => true, 'comment' => 'Service Number']
        );

        $installer->endSetup();
    }
}
