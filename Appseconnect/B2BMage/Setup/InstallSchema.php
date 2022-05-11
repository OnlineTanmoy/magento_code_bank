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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @category B2BMage\Setup
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
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        
        $installer->startSetup();
        
        $parentContext = $context;
        
        $this->setupContactPersonStructure($installer);
        
        $this->setupCreditLimitStructure($installer);
        
        $this->setupTierPriceStructure($installer);
        
        $this->setupSpecialPriceStructure($installer);
        
        $this->setupErpDocsStructure($installer);
        
        $this->setupSalesRepStructure($installer);
        
        $this->setupCategoryDiscountStructure($installer);
        
        $this->setupPricelistStructure($installer);
        
        $installer->endSetup();
    }

    /**
     * Setup ContactPerson Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupContactPersonStructure($installer)
    {
        $tableName = $installer->getTable('insync_contactperson');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create insync_contactperson table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Contactperson Id'
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
                    ], 'Contactperson Id'
                )
                ->addColumn(
                    'contactperson_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Contactperson Id'
                )
                ->setComment('insync_contactperson')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        
        $tableName = $installer->getTable('insync_contactperson_grid');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create insync_contactperson_grid table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'contactperson_grid_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Contactperson Id'
                )
                ->addColumn(
                    'contactperson_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Customer Id'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Customer Id'
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
                ->addColumn(
                    'gender', Table::TYPE_INTEGER, 5, [
                    'nullable' => true
                    ], 'Gender'
                )
                ->addColumn(
                    'is_active', Table::TYPE_TEXT, 100, [
                    'nullable' => false,
                    'default' => 1
                    ], 'Status'
                )
                ->setComment('insync_contactperson')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup CreditLimit Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupCreditLimitStructure($installer)
    {
        $tableName = $installer->getTable('insync_credit_transaction');
        
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Appseconnect Credit Transaction ID'
                )
                ->addColumn(
                    'increment_id', Table::TYPE_TEXT, 255, [
                    'nullable' => true
                    ], 'Order Increment ID'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'B2B Customer ID'
                )
                ->addColumn(
                    'debit_amount', Table::TYPE_DECIMAL, '14,4', [
                    'nullable' => true,
                    'default' => 0
                    ], 'Debit Amount'
                )
                ->addColumn(
                    'credit_amount', Table::TYPE_DECIMAL, '14,4', [
                    'nullable' => true,
                    'default' => 0
                    ], 'Credit Amount'
                )
                ->addColumn(
                    'available_balance', Table::TYPE_DECIMAL, '14,4', [
                    'nullable' => true,
                    'default' => 0
                    ], 'Available Balance'
                )
                ->addColumn(
                    'credit_limit', Table::TYPE_DECIMAL, '14,4', [
                    'nullable' => true,
                    'default' => 0
                    ], 'Credit Limit Balance'
                )
                ->setComment('insync_credit_transaction')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup TierPrice Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupTierPriceStructure($installer)
    {
        $tableName = $installer->getTable('insync_customer_tierprice');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Primary key'
                )
                ->addColumn(
                    'website_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Website Id'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Customer Id'
                )
                ->addColumn(
                    'product_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Product Id'
                )
                ->addColumn(
                    'product_sku', Table::TYPE_TEXT, 200, [
                    'nullable' => true
                    ], 'Sku'
                )
                ->addColumn(
                    'quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Quantity'
                )
                ->addColumn(
                    'tier_price', Table::TYPE_DECIMAL, '14,3', [
                    'nullable' => true
                    ], 'Price'
                )
                ->setComment('insync_customer_tierprice')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup SpecialPrice Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupSpecialPriceStructure($installer)
    {
        $tableName = $installer->getTable('insync_customer_specialprice');
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
                    ], 'Primary key'
                )
                ->addColumn(
                    'website_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Website Id'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Customer Id'
                )
                ->addColumn(
                    'customer_name', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Customer Name'
                )
                ->addColumn(
                    'pricelist_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Pricelist Id'
                )
                ->addColumn(
                    'discount_type', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Discount Type'
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
                    'is_active', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Is Active'
                )
                ->setComment('insync_customer_specialprice')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        
        $tableName = $installer->getTable('insync_specialprice_map');
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
                    ], 'Primary key'
                )
                ->addColumn(
                    'parent_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true
                    ], 'Parent Special Price Id'
                )
                ->addColumn(
                    'product_sku', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Product SKU'
                )
                ->addColumn(
                    'special_price', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Special Price'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'insync_specialprice_map', [
                        'parent_id'
                        ]
                    ), [
                    'parent_id'
                    ]
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'insync_specialprice_map',
                        'parent_id',
                        'insync_customer_specialprice',
                        'id'
                    ),
                    'parent_id',
                    $installer->getTable('insync_customer_specialprice'),
                    'id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->setComment('insync_specialprice_map')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup ErpDocs Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupErpDocsStructure($installer)
    {
        $tableName = $installer->getTable('insync_erp_invoice');
        
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'insync_erp_invoice_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Erp invoice id'
                )
                ->addColumn(
                    'invoice_increment_id', Table::TYPE_TEXT, 255, [
                    'nullable' => true
                    ], 'Invoice increment id'
                )
                ->addColumn(
                    'order_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Order id'
                )
                ->addColumn(
                    'pdf_path', Table::TYPE_TEXT, 255, [
                    'nullable' => true
                    ], 'Order Increment ID'
                )
                ->setComment('insync_erp_invoice_id')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup SalesRep Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupSalesRepStructure($installer)
    {
        $tableName = $installer->getTable('insync_salesrepresentative');
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
                    'salesrep_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                    ], 'Salesrep ID'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                    ], 'Customer ID'
                )
                ->setComment('Appseconnect_Salesrepresentative')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $tableName_insync_salesrep_grid = $installer->getTable('insync_salesrep_grid');
        if ($installer->getConnection()->isTableExists(
            $tableName_insync_salesrep_grid
        ) != true
        ) {
            $table = $installer->getConnection()
                ->newTable($tableName_insync_salesrep_grid)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'auto_increment' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                    ], 'Id'
                )
                ->addColumn(
                    'magento_customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Magento Customer ID'
                )
                ->addColumn(
                    'name', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Salesrep Name'
                )
                ->addColumn(
                    'firstname', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Salesrep First Name'
                )
                ->addColumn(
                    'middlename', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Salesrep Middle Name'
                )
                ->addColumn(
                    'lastname', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Salesrep Last Name'
                )
                ->addColumn(
                    'email', Table::TYPE_TEXT, 155, [
                    'nullable' => true
                    ], 'Email'
                )
                ->addColumn(
                    'gender', Table::TYPE_INTEGER, 5, [
                    'nullable' => true
                    ], 'Gender'
                )
                ->addColumn(
                    'website_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Website ID'
                )
                ->addColumn(
                    'is_active', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'default' => 1
                    ], 'Is Active'
                )
                ->setComment('Appseconnect Salesrep Grid')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup CategoryDiscount Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupCategoryDiscountStructure($installer)
    {
        $tableName = $installer->getTable('insync_categorydiscount');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'categorydiscount_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Primary key'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                    ], 'Customer Id'
                )
                ->addColumn(
                    'category_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                    ], 'Category Id'
                )
                ->addColumn(
                    'discount_factor', Table::TYPE_DECIMAL, '14,3', [
                    'nullable' => false
                    ], 'Discount Amount'
                )
                ->addColumn(
                    'is_active', Table::TYPE_INTEGER, 5, [
                    'nullable' => false,
                    'default' => 1
                    ], 'Is Active'
                )
                ->setComment('insync_categorydiscount')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * Setup Pricelist Structure
     *
     * @param SchemaSetupInterface $installer Installer
     *
     * @return void
     */
    public function setupPricelistStructure($installer)
    {
        $tableName = $installer->getTable('insync_pricelist');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                    ], 'ID'
                )
                ->addColumn(
                    'created_at', Table::TYPE_DATETIME, null, [
                    'nullable' => false
                    ], 'Created At'
                )
                ->addColumn(
                    'pricelist_name', Table::TYPE_TEXT, null, [
                    'nullable' => true,
                    'default' => ''
                    ], 'Pricelist Name'
                )
                ->addColumn(
                    'discount_factor', Table::TYPE_DECIMAL, '10,4', [
                    'nullable' => false
                    ], 'Discount Factor'
                )
                ->setComment('Appseconnect_Pricelist')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $tableName_insync_product_pricelist_map = $installer->getTable('insync_product_pricelist_map');
        if ($installer->getConnection()->isTableExists(
            $tableName_insync_product_pricelist_map
        ) != true
        ) {
            $table = $installer->getConnection()
                ->newTable($tableName_insync_product_pricelist_map)
                ->addColumn(
                    'product_pricelist_map_id', Table::TYPE_INTEGER, null, [
                    'auto_increment' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                    ], 'Product Pricelist Map Id'
                )
                ->addColumn(
                    'pricelist_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Pricelist Id'
                )
                ->addColumn(
                    'pricelist_name', Table::TYPE_TEXT, null, [
                    'nullable' => true,
                    'default' => ''
                    ], 'Pricelist Name'
                )
                ->addColumn(
                    'product_id', Table::TYPE_TEXT, null, [
                    'nullable' => true,
                    'default' => ''
                    ], 'Product Id'
                )
                ->setComment('Appseconnect_Product Pricelist Map')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }
}
