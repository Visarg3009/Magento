<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/order_comment_history'))
    ->addColumn(
        'comment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Comment Id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Order Id'
    )
    ->addColumn(
        'comment',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'comment'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'status'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Created At'
    );
$installer->getConnection()->createTable($table);
$installer->endSetup();
