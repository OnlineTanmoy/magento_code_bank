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

namespace Appseconnect\CompanyDivision\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpgradeSchema implements UpgradeSchemaInterface
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

        // Contact Person
        $this->createDivisionStructure($setup, $context);


        $setup->endSetup();
    }

    /**
     * Update ContactPersonStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function createDivisionStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            $tableName = $setup->getTable('insync_division');
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                // Create insync_contactperson table
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true
                    ], 'primary Id'
                    )
                    ->addColumn(
                        'customer_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                    ], 'Customer Id'
                    )
                    ->addColumn(
                        'is_active', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                        'default' => 1
                    ], 'Division Is Active'
                    )
                    ->addColumn(
                        'division_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                    ], 'Division Id'
                    )
                    ->addColumn(
                        'name', Table::TYPE_TEXT, 200, [
                        'nullable' => true,
                        'default' => ''
                    ], 'Customer Name'
                    )
                    ->addColumn(
                        'email', Table::TYPE_TEXT, 150, [
                        'nullable' => true,
                        'default' => ''
                    ], 'Customer Email'
                    )
                    ->setComment('insync_division')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }
        }

    }

}
