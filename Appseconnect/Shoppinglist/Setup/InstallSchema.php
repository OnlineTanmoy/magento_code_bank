<?php
namespace Appseconnect\Shoppinglist\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'insync_customer_product_list'
         */

        $tableName = $setup->getTable('insync_customer_product_list');
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                ], 'List Id')
                ->addColumn('list_name', Table::TYPE_TEXT, null, [
                    'nullable' => true
                ], 'List Name')
                ->addColumn('customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true,
                ], 'Customer Id')
                ->addColumn('item', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Item Count')
                ->addColumn('total_price', Table::TYPE_DECIMAL, '12,2', [
                    'nullable' => true
                ], 'Total Price')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                    'nullable' => true
                ], 'Created At')
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'customer_id',
                        $setup->getTable('customer_entity'),
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('insync_customer_product_list')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        /**
         * Create table 'insync_customer_product_list_map'
         */

        $tableName = $setup->getTable('insync_customer_product_list_map');
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                ], 'List Id')
                ->addColumn('product_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Product Id')
                ->addColumn('list_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true,
                ], 'List Id')
                ->addColumn('qty', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Item Qty')
                ->addColumn('unit_price', Table::TYPE_DECIMAL, '12,2', [
                    'nullable' => true
                ], 'Unit Price')
                ->addColumn('total_price', Table::TYPE_DECIMAL, '12,2', [
                    'nullable' => true
                ], 'Total Price')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                    'nullable' => true
                ], 'Created At')
                ->addColumn('product_option', Table::TYPE_TEXT, null, [
                    'nullable' => true,
                    'unsigned' => true
                ], 'Product Option')
                ->addColumn('product_type', Table::TYPE_TEXT, 255, [
                    'unsigned' => true,
                    'nullable' => true
                ], 'Product Type')
                ->addColumn('product_addtocart_data', Table::TYPE_TEXT, null, [
                    'unsigned' => true,
                    'nullable' => true
                ], 'Product Addtocart')
                ->addColumn('product_sku', Table::TYPE_TEXT, null, [
                    'unsigned' => true,
                    'nullable' => true
                ], 'Product Sku')
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'list_id',
                        $setup->getTable('insync_customer_product_list'),
                        'entity_id'
                    ),
                    'list_id',
                    $setup->getTable('insync_customer_product_list'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('insync_customer_product_list_map')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        /**
         * Create table 'insync_customer_product_share_list'
         */

        $tableName = $setup->getTable('insync_customer_product_share_list');
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                ], 'Share List Id')
                ->addColumn('share_customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Share Customer Id')
                ->addColumn('list_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true,
                ], 'Product List Id')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                    'nullable' => true
                ], 'Created At')
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'list_id',
                        $setup->getTable('insync_customer_product_list'),
                        'entity_id'
                    ),
                    'list_id',
                    $setup->getTable('insync_customer_product_list'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('insync_customer_product_share_list')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
