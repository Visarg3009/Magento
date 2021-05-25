<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->modifyColumn(
        $installer->getTable('vendor/product_request'),
        'request_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
    );

$installer->endSetup();
