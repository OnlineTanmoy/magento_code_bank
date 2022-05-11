<?php

namespace Appseconnect\ShippingMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @category ShippingMethod\Setup
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

        $this->setupShippingMethodStructure($installer);

        $installer->endSetup();
    }

    /**
     * Setup ShippingMethod Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupShippingMethodStructure($installer)
    {
        $tableName = $installer->getTable('insync_shippingmethod');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create insync_shippingmethod table
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
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Customer Id'
                )
                ->addColumn(
                    'shipping_type', Table::TYPE_TEXT, 100, [
                    'nullable' => true
                ], 'Shipping Type'
                )
                ->addColumn(
                    'status', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                ], 'Status'
                )
                ->addColumn(
                    'minimum_order_value', Table::TYPE_DECIMAL, '14,2', [
                    'nullable' => true
                ], 'Minimum Order Value'
                )
                ->setComment('insync_shippingmethod')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }
}