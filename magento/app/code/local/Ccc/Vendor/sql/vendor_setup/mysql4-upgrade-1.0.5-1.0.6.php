<?php

$installer = $this;
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_datetime'),
    'UNQ_VENDOR_DATETIME',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_int'),
    'UNQ_VENDOR_INT',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_text'),
    'UNQ_VENDOR_TEXT',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_char'),
    'UNQ_VENDOR_CHAR',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_varchar'),
    'UNQ_VENDOR_VARCHAR',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);
$installer->getConnection()->addKey(
    $installer->getTable('vendor/vendor_product_decimal'),
    'UNQ_VENDOR_DECIMAL',
    array('attribute_id', 'store_id', 'entity_id'),
    'unique'
);

$installer->endSetup();
