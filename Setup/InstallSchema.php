<?php
/**
 * Raguvis CmsNavigation
 */

namespace Raguvis\CmsNavigation\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Add Include in navigation flag
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'show_in_navigation',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'comment' => 'Flag whether or not CMS page should be included in top navigation',
                'default' => 0
            ]
        );
        $installer->endSetup();
    }
}
