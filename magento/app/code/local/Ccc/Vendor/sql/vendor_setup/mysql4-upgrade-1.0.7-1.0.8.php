<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('vendor/product'),
        'parent_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => 0,
        ),
        'Parent Id'
    );

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'vendor/product',
        'parent_id',
        'vendor/vendor',
        'entity_id'
    ),
    $installer->getTable('vendor/product'),
    'parent_id',
    $installer->getTable('vendor/vendor'),
    'entity_id'
);

$installer->endSetup();
