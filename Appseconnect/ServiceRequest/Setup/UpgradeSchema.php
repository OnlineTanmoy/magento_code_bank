<?php

namespace Appseconnect\ServiceRequest\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            $tableName = $setup->getTable('insync_product_warranty');
            $setup->getConnection()->addColumn(
                $tableName, 'date_of_purchase', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true, 'comment' => 'Date of Purchase']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'store_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true, 'comment' => 'Store ID']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'terms_condition', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => true, 'primary' => false, 'comment' => 'Terms and condition']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'purchase_order_number', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50, 'nullable' => true, 'default' => '', 'comment' => 'purchase order number']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'purchase_order_file', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 100, 'nullable' => true, 'default' => '', 'comment' => 'purchase order file']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'submit_date', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, 'comment' => 'Submit Date']
            );
        }

        if(version_compare($context->getVersion(), '0.0.3') < 0) {
            // Product Serial table
            $tableName = $setup->getTable('insync_product_serial');
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'Fixed Repair Id')
                    ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50,
                        ['nullable' => true, 'default' => ''], 'SKU')
                    ->addColumn('serial_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 100,
                        ['nullable' => true, 'default' => ''], 'Serial No.')
                    ->addColumn('warranty_months', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                        ['nullable' => false, 'default' => 6], 'Warranty in Months')
                    ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At')
                    ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                        ['nullable' => false, 'default' => 0], 'Warrenty Active')
                    ->addColumn('assign_customer', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                        ['nullable' => false, 'default' => 0], 'Assign Customer')
                    ->setComment('insync_product_serial')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }
            
        }

        $setup->endSetup();
    }
}
