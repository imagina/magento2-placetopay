<?php

namespace Imagina\Placetopay\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
 
class InstallSchema implements InstallSchemaInterface
{

    protected $logger;

    public function __construct(
        \Imagina\Placetopay\Logger\Logger $logger,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
    }


    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->logger->info("Inicia Install");

        $tableName   = $this->resource->getTableName('sales_order');

        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
 
        
        if ($connection->tableColumnExists($tableName, 'request_id') === false) {
            $connection
                ->addColumn(
                    $setup->getTable($tableName),
                    'request_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'visible' => false,
                        'comment' => 'Request id to Placetopay'
                    ]
                );
        }

        $installer->endSetup();

    }
}