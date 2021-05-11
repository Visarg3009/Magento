<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('vendor/eav_attribute'),
        'sort_order',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false,
            'default' => 0
        ),
        'Sort Order'
    );

$installer->endSetup();
