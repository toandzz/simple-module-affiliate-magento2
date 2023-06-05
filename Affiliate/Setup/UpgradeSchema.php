<?php
namespace Mageplaza\Affiliate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {

        $installer = $setup;

        $installer->startSetup();


        if(version_compare($context->getVersion(), '4.0.0', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable( 'quote' ),
                'affiliate_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 255,
                    'comment' => 'Affiliate Code',
                    'after' => 'is_active'
                ]
                );
                $installer->getConnection()->addColumn(
                    $installer->getTable( 'quote' ),
                    'discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'size' => '12,2',
                        'comment' => 'Discount',
                        'after' => 'affiliate_code'
                    ]
                );
        }
        $installer->endSetup();
    }
}
