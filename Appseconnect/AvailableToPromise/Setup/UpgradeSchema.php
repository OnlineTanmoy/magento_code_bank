<?php

namespace Appseconnect\AvailableToPromise\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.1', '<')) {
            $this->addAvailableToPromiseTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function addAvailableToPromiseTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('insync_availabletopromise');
        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'availabletopromise_id'
            )
            ->addColumn('available_date', Table::TYPE_DATETIME, null, ['nullable' => false])
            ->addColumn('product_sku', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('quantity', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('available_quantity', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('document_type', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('warehouse', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('sync_flag', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('posting_date', Table::TYPE_DATETIME, null, ['nullable' => false]);
        $setup->getConnection()->createTable($table);

        $table = $setup->getTable('quote');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'delivery_info',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'delivery_info'
            ]
        );

        $table = $setup->getTable('quote_item');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'delivery_info',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'delivery_info'
            ]
        );

        $table = $setup->getTable('sales_order');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'delivery_info',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'delivery_info'
            ]
        );

        $table = $setup->getTable('sales_order_item');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'delivery_info',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'delivery_info'
            ]
        );
    }

}
