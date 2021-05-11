<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($this->getTable('vendor/product_attribute_group'))
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Attribute ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
    ), 'Is Global')
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
    ), 'Is Visible')
    ->addColumn('attribute_group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Is Searchable')
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_attribute_group',
            'entity_id',
            'vendor/vendor',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('vendor/vendor'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_attribute_group',
            'attribute_group_id',
            'eav/attribute_group',
            'attribute_group_id'
        ),
        'attribute_group_id',
        $installer->getTable('eav/attribute_group'),
        'attribute_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Vendor EAV Attribute Group Table');

$installer->getConnection()->createTable($table);
$installer->endSetup();
