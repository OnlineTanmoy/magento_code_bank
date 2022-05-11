<?php

namespace Appseconnect\MultipleDiscounts\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @category MultipleDiscounts\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * Upgrade function
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->addBrandsAttribute($setup, $context);

        $this->setupQuoteItemDiscountId($setup, $context);

        $setup->endSetup();
    }

    /**
     * Add Brands attribute
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function addBrandsAttribute($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.2') < 0) {

            $tableName = $setup->getTable('insync_multiple_discount');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'brands' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Brands'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
    }

    /**
     * Add Discount Id attribute
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function setupQuoteItemDiscountId($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.3') < 0) {

            $tableName = $setup->getTable('quote_item');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'discount_id' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Discount Id'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
    }
}