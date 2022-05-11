<?php

namespace Appseconnect\MultipleDiscounts\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @category MultipleDiscounts\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install function
     *
     * @param SchemaSetupInterface $setup Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $parentContext = $context;

        $this->setupMultipleDiscountsStructure($installer);

        $installer->endSetup();
    }

    /**
     * Setup MultipleDiscounts Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupMultipleDiscountsStructure($installer)
    {
        $tableName = $installer->getTable('insync_multiple_discount');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create insync_multiple_discount table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                ], 'Id'
                )
                ->addColumn(
                    'rule_name', Table::TYPE_TEXT, 255, [
                    'nullable' => true
                ], 'Rule Name'
                )
                ->addColumn(
                    'description', Table::TYPE_TEXT, 255, [
                    'nullable' => true
                ], 'Description'
                )
                ->addColumn(
                    'is_active', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Is Active'
                )
                ->addColumn(
                    'website_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Website Id'
                )
                ->addColumn(
                    'start_date', Table::TYPE_DATE, null, [
                    'nullable' => true
                ], 'Start Date'
                )
                ->addColumn(
                    'end_date', Table::TYPE_DATE, null, [
                    'nullable' => true
                ], 'End Date'
                )
                ->addColumn(
                    'discount_type', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Discount Type'
                )
                ->addColumn(
                    'product_variation', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Product Variation'
                )
                ->addColumn(
                    'first_product_sku', Table::TYPE_TEXT, 100, [
                    'nullable' => true
                ], 'First Product Sku'
                )
                ->addColumn(
                    'second_product_sku', Table::TYPE_TEXT, 100, [
                    'nullable' => true
                ], 'Second Product Sku'
                )
                ->addColumn(
                    'first_product_quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'First Product Quantity'
                )
                ->addColumn(
                    'second_product_quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Second Product Quantity'
                )
                ->addColumn(
                    'discount_turner', Table::TYPE_TEXT, 100, [
                    'nullable' => true
                ], 'Discount Turner'
                )
                ->addColumn(
                    'minimum_order_amount', Table::TYPE_DECIMAL, '14,2', [
                    'nullable' => true
                ], 'Minimum Order Amount'
                )
                ->addColumn(
                    'minimum_item_quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Minimum Item Quantity'
                )
                ->addColumn(
                    'discount_quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Discount Quantity'
                )
                ->setComment('insync_multiple_discount')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable('insync_multiple_discount_map');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                ], 'ID'
                )
                ->addColumn(
                    'parent_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false,
                    'unsigned' => true
                ], 'Parent Multiple Discount Id'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                ], 'Customer ID'
                )
                ->addIndex($installer->getIdxName('insync_multiple_discount_map', [
                    'parent_id'
                ]), [
                    'parent_id'
                ])
                ->addForeignKey(
                    $installer->getFkName(
                        'insync_multiple_discount_map',
                        'parent_id',
                        'insync_multiple_discount',
                        'id'
                    ),
                    'parent_id',
                    $installer->getTable('insync_multiple_discount'),
                    'id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->setComment('insync_multiple_discount_map')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }
}