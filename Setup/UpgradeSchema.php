<?php

namespace Kamlesh\Promotional\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            
            // Create promotional_products table
            if (!$installer->tableExists('promotional_products')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('promotional_products')
                )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product ID'
                )
                ->addColumn(
                    'discount_percentage',
                    Table::TYPE_DECIMAL,
                    '5,2',
                    ['nullable' => false, 'default' => '0.00'],
                    'Discount Percentage'
                )
                ->addColumn(
                    'start_date',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false],
                    'Start Date'
                )
                ->addColumn(
                    'end_date',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false],
                    'End Date'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Status (Active)'
                )
                ->addForeignKey(
                    $installer->getFkName('promotional_products', 'product_id', 'catalog_product_entity', 'entity_id'),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Promotional Products Table');
                $installer->getConnection()->createTable($table);
            }
        }

        $installer->endSetup();
    }
}