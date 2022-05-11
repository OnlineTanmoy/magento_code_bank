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
    const CUSTOM_ATTRIBUTE_ID = 'customer_type';

    const CUSTOMER_STATUS = 'customer_status';

    const CUSTOMER_CREDIT_LIMIT = 'customer_credit_limit';

    const CUSTOMER_AVAILABLE_BALANCE = 'customer_available_balance';

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
        $this->updateContactPersonStructure($setup, $context);

        // Customer Special Price
        $this->updateSpecialPriceStructure($setup, $context);
        // Customer Specificdiscount
        $this->updateSpecificDiscountStructure($setup, $context);
        // Customer Tierprice
        $this->updateTierPriceStructure($setup, $context);
        // Pricelist
        $this->updatePricelistStructure($setup, $context);
        // Sales Order
        $this->updateSalesStructure($setup, $context);
        // Salesrep
        $this->updateSalesRepStructure($setup, $context);
        // Quotation
        $this->updateQuotationStructure($setup, $context);

        $this->updateSpecificDiscountAmountStructure($setup, $context);

        $this->setupMobileTheme($setup, $context);

        $this->setupMobileThemeApi($setup, $context);

        $this->addPricelistAttribute($setup, $context);

        $this->updateCategoryDiscountStructure($setup, $context);

        // Customer TierPrice
        $this->updateTierPriceMinimumOrderStructure($setup, $context);

        // Customer Special Price Pricelist Structure
        $this->updateSpecialPricePricelistStructure($setup, $context);

        // Customer Tier Price Pricelist Structure
        $this->updateTierPricePricelistStructure($setup, $context);

        $setup->endSetup();
    }

    /**
     * Add Tier Price Pricelist Name attribute
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function updateTierPricePricelistStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.28') < 0) {

            $tableName = $setup->getTable('insync_customer_tierprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'pricelist_name' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Pricelist Name'
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
     * Add Special Price Pricelist Name attribute
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function updateSpecialPricePricelistStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.27') < 0) {

            $tableName = $setup->getTable('insync_customer_specialprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'pricelist_name' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Pricelist Name'
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
     * Update TierPriceMinimumOrderStructure
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function updateTierPriceMinimumOrderStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.26') < 0) {

            $tableName = $setup->getTable('insync_customer_tierprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'minimum_order_amount' => [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '10,2',
                        'nullable' => true,
                        'default'   => 0.00,
                        'comment' => 'Minimum Order Amount'
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
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function updateCategoryDiscountStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.25') < 0) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('insync_categorydiscount'),
                'discount_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Discount Type'
            );
        }
    }

    /**
     * Update ContactPersonStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateContactPersonStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_contact_address');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                // Declare data
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'insync_contact_address_id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true
                        ], 'Address Id'
                    )
                    ->addColumn(
                        'customer_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Customer Id'
                    )
                    ->addColumn(
                        'contact_person_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Contact person id'
                    )
                    ->addColumn(
                        'customer_address_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Contact person id'
                    )
                    ->addColumn(
                        'contect_person_address_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Contact person address id'
                    )
                    ->addColumn(
                        'is_billing', Table::TYPE_BOOLEAN, null, [
                        'nullable' => true,
                        'default' => 0
                        ], 'is billable'
                    )
                    ->addColumn(
                        'is_shipping', Table::TYPE_BOOLEAN, null, [
                        'nullable' => true,
                        'default' => 0
                        ], 'is shipping'
                    )
                    ->setComment('insync_contactperson')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }

            $setup->getConnection()->addColumn(
                $setup->getTable('customer_entity'),
                self::CUSTOM_ATTRIBUTE_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 128,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '1',
                    'comment' => 'Customer Type (
                                            1=Customer 
                                            2=Salesrepresentative 
                                            3=Contact Person)'
                ]
            );



            $setup->getConnection()->addColumn(
                $setup->getTable('customer_entity'),
                self::CUSTOMER_STATUS,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 2,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '1',
                    'comment' => 'Customer Status (1=Active 0=Inactive)'
                ]
            );



            $setup->getConnection()->dropColumn(
                $setup->getTable('customer_entity'),
                self::CUSTOMER_STATUS
            );
          

            $setup->getConnection()->addColumn(
                $setup->getTable('customer_entity'),
                self::CUSTOMER_STATUS,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 2,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '1',
                    'comment' => 'Customer Status (1=Active 0=Inactive)'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.20') < 0) {
            $tableName = $setup->getTable('insync_contactperson');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                //contact person table update
                $setup->getConnection()->changeColumn(
                    $tableName,
                    'customer_id',
                    'customer_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Customer Id'
                );

                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        $tableName, [
                            'customer_id'
                        ]
                    ),
                    [
                        'customer_id'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                );
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'customer_id',
                        $setup->getTable('customer_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        $tableName, [
                            'contactperson_id'
                        ]
                    ),
                    [
                        'contactperson_id'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                );
            }
            //contact person table update
        }
    }

    /**
     * Update QuotationStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateQuotationStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.15') < 0) {
            $this->setupCustomerQuotationStructure($setup);
            $this->setupQuotationItemStructure($setup);

            $tableName = $setup->getTable('insync_customer_quote_product');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName($tableName, ['quote_id']),
                    ['quote_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                );
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'quote_id',
                        $setup->getTable('insync_customer_quote'),
                        'id'
                    ),
                    $setup->getTable($tableName),
                    'quote_id',
                    $setup->getTable('insync_customer_quote'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }

            $this->restructureCustomerQuotation($setup);
            $this->restructureQuotationItem($setup);
            $this->setupQuotationStatusStructure($setup);
            $this->setupQuotationHistoryStructure($setup);

            $tableName = $setup->getTable('insync_customer_quote_product');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'super_attribute' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Super Attribute Value'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('quote_item');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->addColumn(
                    $tableName,
                    "custom_quotation_price",
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '10,2',
                        'comment' => 'Custom Quotation Price'
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '0.0.21') < 0) {
            $columns = [
                'quotation_info' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Quotation Information'
                ]
            ];


            $tableName = $setup->getTable('quote');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('sales_order');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
    }

    /**
     * Setup CustomerQuotation Structure
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function setupCustomerQuotationStructure($setup)
    {
        $tableName = $setup->getTable('insync_customer_quote');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'auto_increment' => true
                    ], 'Quote Id'
                )
                ->addColumn(
                    'customer_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Customer Id'
                )
                ->addColumn(
                    'contact_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false
                    ], 'Contactperson Id'
                )
                ->addColumn(
                    'grand_total_negotiated',
                    Table::TYPE_DECIMAL,
                    null,
                    [
                        'length' => 10, 2,
                        'unsigned' => true,
                        'nullable' => true
                    ],
                    'Grand Total Negotiated'
                )
                ->addColumn(
                    'base_grand_total_negotiated',
                    Table::TYPE_DECIMAL,
                    null,
                    [
                        'length' => 10, 2,
                        'unsigned' => true,
                        'nullable' => true
                    ],
                    'Base Grand Total Negotiated'
                )
                ->addColumn(
                    'status', Table::TYPE_TEXT, null, [
                    'length' => 128,
                    'nullable' => true
                    ], 'Status'
                )->setComment('insync_customer_quote')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Setup QuotationItem Structure
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function setupQuotationItemStructure($setup)
    {
        $tableName = $setup->getTable('insync_customer_quote_product');

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
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
                    'quote_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true
                    ], 'Quote Id'
                )
                ->addColumn(
                    'product_id', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Product Id'
                )
                ->addColumn(
                    'product_sku', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Product Sku'
                )
                ->addColumn(
                    'qty', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Product Qty'
                )
                ->setComment('insync_customer_quote_product')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Setup QuotationStatusStructure
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function setupQuotationStatusStructure($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('insync_quotation_status'))
            ->addColumn(
                'status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, [
                'nullable' => false,
                'primary' => true
                ], 'Status'
            )
            ->addColumn(
                'label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 128, [
                'nullable' => false
                ], 'Label'
            )
            ->setComment('Quotation Status Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Setup QuotationHistory Structure
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function setupQuotationHistoryStructure($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('insync_quotation_status_history'))
            ->addColumn(
                'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
                ], 'Entity Id'
            )
            ->addColumn(
                'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned' => true,
                'nullable' => false
                ], 'Parent Id'
            )
            ->addColumn(
                'is_customer_notified',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Is Customer Notified'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Name'
            )
            ->addColumn(
                'is_visible_on_front',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Is Visible On Frontend'
            )
            ->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Comments'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [],
                'Quote Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ],
                'Creation Timestamp'
            )
            ->addColumn(
                'entity_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [
                    'nullable' => true
                ],
                'Entity Name'
            )
            ->addIndex(
                $setup->getIdxName(
                    'insync_quotation_status_history', [
                        'parent_id'
                    ]
                ), [
                    'parent_id'
                ]
            )
            ->addIndex(
                $setup->getIdxName(
                    'insync_quotation_status_history', [
                        'created_at'
                    ]
                ), [
                    'created_at'
                ]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'insync_quotation_status_history',
                    'parent_id',
                    'insync_customer_quote',
                    'id'
                ),
                'parent_id',
                $setup->getTable('insync_customer_quote'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Sales Flat Order Status History');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Restructure CustomerQuotation
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function restructureCustomerQuotation($setup)
    {
        $tableName = $setup->getTable('insync_customer_quote');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'store_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Store Id'
                ],
                'created_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Created At'
                ],
                'updated_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Updated At'
                ],
                'customer_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Customer Name'
                ],
                'customer_email' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Customer Email'
                ],
                'customer_group_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Customer Group Id'
                ],
                'contact_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Contact Name'
                ],
                'contact_email' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Contact Email'
                ],
                'contact_group_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Contact Group Id'
                ],
                'subtotal' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Subtotal'
                ],
                'base_subtotal' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Subtotal'
                ],
                'grand_total' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Grand Total'
                ],
                'base_grand_total' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Grand Total'
                ],
                'store_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Store Name'
                ],
                'proposed_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Proposed Price'
                ],
                'base_proposed_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Proposed Price'
                ],
                'is_converted' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Is Convered to Order'
                ],
                'items_qty' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Items Qty'
                ],
                'items_count' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Items Count'
                ],
                'base_currency_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Base Currency Code'
                ],
                'store_currency_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Store Currency Code'
                ],
                'quotation_currency_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Quotation Currency Code'
                ],
                'is_active' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Is Active'
                ],
                'customer_is_guest' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Customer Is Guest'
                ],
                'customer_gender' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Gender'
                ],
                'increment_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Increment Id'
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }

    /**
     * Restructure Quotation Item
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function restructureQuotationItem($setup)
    {
        $tableName = $setup->getTable('insync_customer_quote_product');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $columns = [
                'row_total' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Row Total'
                ],
                'base_row_total' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Row Total'
                ],
                'price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Price'
                ],
                'base_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Price'
                ],
                'original_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Original Price'
                ],
                'base_original_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'nullable' => true,
                    'comment' => 'Base Original Price'
                ],
                'parent_item_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Parent Item Id'
                ],
                'weight' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Weight'
                ],
                'name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Product Name'
                ],
                'product_type' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Product Type'
                ],
                'is_virtual' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Is Virtual'
                ],
                'store_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Store Id'
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }

    /**
     * Update TierPriceStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateTierPriceStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.4') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_customer_tierprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'customer_name' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Customer Name',
                        'after' => 'customer_id'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('insync_customer_tierprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'pricelist_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Pricelist Id',
                        'after' => 'customer_name'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('insync_customer_tierprice');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'is_active' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Is Active'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('insync_customer_tierprice');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                $connection->dropColumn($tableName, 'product_id');
            }

            $tableName = $setup->getTable('insync_customer_tierprice');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                $connection->dropColumn($tableName, 'product_sku');
                $connection->dropColumn($tableName, 'tier_price');
                $connection->dropColumn($tableName, 'quantity');
            }
            $this->restructureTierPrice($setup);
        }
    }

    /**
     * Restructure TierPrice
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function restructureTierPrice($setup)
    {
        $tableName = $setup->getTable('insync_customer_tierprice');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $columns = [
                'discount_type' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Discount Type',
                    'after' => 'pricelist_id'
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        $tableName = $setup->getTable('insync_tierprice_map');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Declare data
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                    ], 'Customer Product Tier Price Map Id'
                )
                ->addColumn(
                    'parent_id', Table::TYPE_INTEGER, null, [
                    'nullable' => false,
                    'unsigned' => true
                    ], 'Parent Tier Price Id'
                )
                ->addColumn(
                    'product_sku', Table::TYPE_TEXT, null, [
                    'nullable' => true
                    ], 'Product SKU'
                )
                ->addColumn(
                    'quantity', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Quantity'
                )
                ->addColumn(
                    'tier_price', Table::TYPE_INTEGER, null, [
                    'nullable' => true
                    ], 'Tier Price'
                )
                ->addIndex(
                    $setup->getIdxName(
                        'insync_tierprice_map', [
                            'parent_id'
                        ]
                    ), [
                        'parent_id'
                    ]
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'insync_tierprice_map',
                        'parent_id',
                        'insync_customer_tierprice',
                        'id'
                    ),
                    'parent_id',
                    $setup->getTable('insync_customer_tierprice'),
                    'id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->setComment('insync_tierprice_map')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        $tableName = $setup->getTable('insync_tierprice_map');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->changeColumn(
                $tableName,
                'tier_price',
                'tier_price',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '14,2',
                    'nullable' => true
                ],
                'Tier price'
            );
        }
    }

    /**
     * Update SpecialPriceStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateSpecialPriceStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_customer_specialprice');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $setup->getConnection()->addIndex(
                    $tableName, $setup->getIdxName(
                        $tableName, [
                        'customer_name'
                        ], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ), [
                    'customer_name'
                    ], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                );
            }

            $tableName = $setup->getTable('insync_specialprice_map');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->changeColumn(
                    $tableName,
                    'special_price',
                    'special_price',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '14,2',
                        'nullable' => true
                    ],
                    'Special Price'
                );
            }

            $tableName = $setup->getTable('insync_specialprice_map');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'product_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Product Id',
                        'length' => '10'
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
     * Update SalesStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateSalesStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.6') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_approver');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                // Declare data
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'insync_approver_id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true
                        ], 'Approver Id'
                    )
                    ->addColumn(
                        'customer_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Customer Id'
                    )
                    ->addColumn(
                        'contact_person_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Contact person id'
                    )
                    ->addColumn(
                        'from', Table::TYPE_DECIMAL, '12,2', [
                        'nullable' => true
                        ], 'From'
                    )
                    ->addColumn(
                        'to', Table::TYPE_DECIMAL, '12,2', [
                        'nullable' => true
                        ], 'To'
                    )
                    ->setComment('insync_approver')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }

            $tableName = $setup->getTable('insync_order_approval');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                // Declare data
                $table = $setup->getConnection()
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
                        'increment_id', Table::TYPE_TEXT, 255, [
                        'nullable' => true
                        ], 'Order increament id'
                    )
                    ->addColumn(
                        'customer_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Customer id'
                    )
                    ->addColumn(
                        'contact_person_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Contact person id'
                    )
                    ->addColumn(
                        'grand_total', Table::TYPE_DECIMAL, '12,2', [
                        'nullable' => true
                        ], 'Grand total'
                    )
                    ->addColumn(
                        'status', Table::TYPE_TEXT, 100, [
                        'nullable' => false,
                        'default' => 'On hold'
                        ], 'Status'
                    )
                    ->setComment('insync_order_approval')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }

            $tableName = $setup->getTable('sales_order');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->addColumn(
                    $tableName, "contact_person_id", [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Contact Person Id'
                    ]
                );
            }
        }
    }

    /**
     * Update PricelistStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updatePricelistStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.5') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_pricelist');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'is_active' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Is Active'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('insync_product_pricelist_map');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'discounted_price' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'comment' => 'Discounted Price',
                        'LENGTH' => '14,3'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $setup->getTable('insync_product_pricelist_map');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'original_price' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'comment' => 'Original Product Price',
                        'LENGTH' => '14,3'
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $this->restructureProductPricelist($setup);
            $this->restructurePricelist($setup);
        }
        if (version_compare($context->getVersion(), '0.0.21') < 0) {
            $tableName = $setup->getTable('insync_product_pricelist_map');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->changeColumn(
                    $tableName,
                    'product_id',
                    'product_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true,
                        'comment' => 'Product Id'
                    ]
                );

                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        $tableName, [
                            'product_id'
                        ]
                    ),
                    [
                        'product_id'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                );
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'product_id',
                        $setup->getTable('catalog_product_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'product_id',
                    $setup->getTable('catalog_product_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }
    }

    /**
     * Restructure ProductPricelist
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function restructureProductPricelist($setup)
    {
        $tableName = $setup->getTable('insync_product_pricelist_map');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $columns = [
                'original_price' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Original Product Price',
                    'LENGTH' => '14,3'
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        $tableName = $setup->getTable('insync_pricelist');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $columns = [
                'website_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Associated Website Id'

                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        $tableName = $setup->getTable('insync_pricelist');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $connection = $setup->getConnection();
            $connection->changeColumn(
                $tableName, 'discount_factor', 'discount_factor', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'LENGTH' => '14,3'
                ], 'Discount Factor'
            );
        }
    }

    /**
     * Restructure Pricelist
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function restructurePricelist($setup)
    {
        $tableName = $setup->getTable('insync_product_pricelist_map');

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $connection = $setup->getConnection();

            $connection->dropColumn($tableName, 'pricelist_id');
        }

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addColumn(
                $setup->getTable($tableName), 'pricelist_id', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Parent Pricelist ID'
                ]
            );
        }

        $tableName = $setup->getTable('insync_product_pricelist_map');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addIndex(
                $tableName, $setup->getIdxName(
                    $tableName, [
                    'pricelist_id'
                    ]
                ), [
                'pricelist_id'
                ], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
        }

        $tableName = $setup->getTable('insync_product_pricelist_map');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                    $tableName,
                    'pricelist_id',
                    $setup->getTable('insync_pricelist'),
                    'id'
                ),
                $setup->getTable($tableName),
                'pricelist_id',
                $setup->getTable('insync_pricelist'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }

        $tableName = $setup->getTable('insync_product_pricelist_map');

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $connection = $setup->getConnection();

            $connection->dropColumn($tableName, 'pricelist_name');
            $connection->dropColumn($tableName, 'discounted_price');
        }

        $this->addColoumsToPricelistStructure($setup);
    }

    /**
     * Add Coloums To PricelistStructure
     *
     * @param SchemaSetupInterface $setup Setup
     *
     * @return void
     */
    public function addColoumsToPricelistStructure($setup)
    {
        $tableName = $setup->getTable('insync_product_pricelist_map');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addColumn(
                $setup->getTable($tableName), 'sku', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 128,
                    'nullable' => true,
                    'comment' => 'Product Sku'
                ]
            );
        }

        $tableName = $setup->getTable('insync_product_pricelist_map');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addColumn(
                $setup->getTable($tableName), 'final_price', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 128,
                    'nullable' => true,
                    'comment' => 'Final Price'
                ]
            );
        }

        $tableName = $setup->getTable('insync_pricelist');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $setup->getConnection()->addColumn(
                $setup->getTable($tableName), 'currency', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 128,
                    'nullable' => true,
                    'comment' => 'Currency'
                ]
            );
        }

        $tableName = $setup->getTable('insync_product_pricelist_map');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $connection = $setup->getConnection();
            $connection->changeColumn(
                $tableName, 'final_price', 'final_price', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '14,2'
                ], 'Final Price'
            );
        }
    }

    /**
     * Update SalesRepStructure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateSalesRepStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.7') < 0) {
            // Get module table
            $tableName = $setup->getTable('insync_salesrep_grid');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $connection = $setup->getConnection();
                $connection->changeColumn(
                    $tableName,
                    'magento_customer_id',
                    'salesrep_customer_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true
                    ],
                    'Sales Representative Customer Id'
                );
            }

            $tableName = $setup->getTable('insync_salesrepresentative');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                $connection->dropColumn($tableName, 'salesrep_id');
            }

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName), 'salesrep_id', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 10,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Parent Salesrep ID'
                    ]
                );
                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        $tableName, [
                            'salesrep_id'
                        ]
                    ),
                    [
                        'salesrep_id'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                );
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'salesrep_id',
                        $setup->getTable('insync_salesrep_grid'),
                        'id'
                    ),
                    $setup->getTable($tableName),
                    'salesrep_id',
                    $setup->getTable('insync_salesrep_grid'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }
    }

    /**
     * Update SpecificDiscount Structure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateSpecificDiscountStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            $tableName = $setup->getTable('sales_order');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName),
                    'customer_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Customer Specific Discount'
                    ]
                );
            }
        }
    }

    /**
     * Update SpecificDiscountAmount Structure
     *
     * @param SchemaSetupInterface   $setup   Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function updateSpecificDiscountAmountStructure($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.18') < 0) {
            $tableName = $setup->getTable('sales_order');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName),
                    'customer_discount_amount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '14,2',
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Customer Specific Discount Amount'
                    ]
                );
            }

            $tableName = $setup->getTable('sales_invoice');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName),
                    'customer_discount_amount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '14,2',
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Customer Specific Discount Amount'
                    ]
                );
            }
        }
    }


    /**
     * Setup MobileTheme
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function setupMobileTheme($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.22') < 0) {
            $tableName = $setup->getTable('insync_mobile_theme');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) != true) {
                // Declare data
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true
                        ], 'Appseconnect Mobile theme Id'
                    )
                    ->addColumn(
                        'website_id', Table::TYPE_INTEGER, null, [
                        'nullable' => true
                        ], 'Website ID'
                    )
                    ->addColumn(
                        'organisation_name', Table::TYPE_TEXT, 255, [
                        'nullable' => true
                        ], 'Organization Name'
                    )
                    ->addColumn(
                        'phone_number', Table::TYPE_TEXT, 20, [
                        'nullable' => true
                        ], 'Phone NUmbar'
                    )
                    ->addColumn(
                        'mobile_account_id', Table::TYPE_TEXT, 255, [
                        'nullable' => true
                        ], 'Mobile Account Id'
                    )
                    ->setComment('insync_mobile_theme')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $setup->getConnection()->createTable($table);
            }
        }
    }

    /**
     * Setup MobileThemeApi
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function setupMobileThemeApi($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.23') < 0) {
            $tableName = $setup->getTable('core_config_data');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $data = [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => 'insync_mobile/theme/api_url',
                    'value' => 'https://api.b2beconnect.co/api/',
                ];
                $setup->getConnection()
                    ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);

                $data = [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => 'insync_mobile/theme/appid',
                    'value' => 'EFAEA9D5-B3A8-4B82-9670-AB8B1DB9837D',
                ];
                $setup->getConnection()
                    ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
            }
        }
    }

    /**
     * Add Pricelist attribute
     *
     * @param $setup   Setup
     * @param $context Context
     *
     * @return void
     */
    public function addPricelistAttribute($setup, $context)
    {
        if (version_compare($context->getVersion(), '0.0.24') < 0) {
            $tableName = $setup->getTable('insync_product_pricelist_map');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'is_manual' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Is Manual price',
                        'LENGTH' => '5'
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
