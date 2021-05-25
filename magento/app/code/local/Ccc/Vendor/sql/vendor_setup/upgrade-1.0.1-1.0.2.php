<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addKey($installer->getTable('vendor/vendor_int'),
    'unique_vendor_int',
    [
        'attribute_id', 'entity_id', 'store_id'
    ],
    'unique');

$installer->getConnection()->addKey($installer->getTable('vendor/vendor_text'),
'unique_vendor_int',
[
    'attribute_id', 'entity_id', 'store_id'
],
'unique');

$installer->getConnection()->addKey($installer->getTable('vendor/vendor_char'),
'unique_vendor_int',
[
    'attribute_id', 'entity_id', 'store_id'
],
'unique');

$installer->getConnection()->addKey($installer->getTable('vendor/vendor_datetime'),
'unique_vendor_int',
[
    'attribute_id', 'entity_id', 'store_id'
],
'unique');

$installer->getConnection()->addKey($installer->getTable('vendor/vendor_decimal'),
'unique_vendor_int',
[
    'attribute_id', 'entity_id', 'store_id'
],
'unique');

$installer->endSetup();