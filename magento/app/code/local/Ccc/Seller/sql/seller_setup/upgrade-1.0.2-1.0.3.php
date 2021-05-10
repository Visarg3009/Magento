<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addKey(
    $installer->getTable('seller/seller_int'),
    'unique_seller_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique'
);

$installer->getConnection()->addKey(
    $installer->getTable('seller/seller_text'),
    'unique_seller_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique'
);

$installer->getConnection()->addKey(
    $installer->getTable('seller/seller_char'),
    'unique_seller_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique'
);

$installer->getConnection()->addKey(
    $installer->getTable('seller/seller_datetime'),
    'unique_seller_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique'
);

$installer->getConnection()->addKey(
    $installer->getTable('seller/seller_decimal'),
    'unique_seller_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique'
);

$installer->endSetup();
