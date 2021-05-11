<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'sku',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'SKU',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'name',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Name',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'description',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Description',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'short_description',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Short Description',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'weight',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Weight',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'status',
    [
        'group' => 'General',
        'input' => 'select',
        'type' => 'varchar',
        'label' => 'Status',
        'backend' => '',
        'visible' => 1,
        'source' => 'catalog/product_status',
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'price',
    [
        'group' => 'Prices',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Price',
        'backend' => '',
        'visible' => 1,
        'required' => 1,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

// $installer->addAttribute(
//     Ccc_Vendor_Model_Product::ENTITY,
//     'vendor_product_request_status',
//     [
//         'group' => 'Common',
//         'input' => 'select',
//         'type' => 'varchar',
//         'label' => 'Request',
//         'backend' => '',
//         'visible' => 1,
//         'source' => 'eav/entity_attribute_source_table',
//         'option' => [
//             'values' => [
//                 'add' => 'Add',
//                 'edit' => 'Edit',
//                 'deleted' => 'Deleted',
//             ],
//         ],
//         'required' => 1,
//         'user_defined' => 0,
//         'searchable' => 0,
//         'filterable' => 0,
//         'comparable' => 0,
//         'visible_on_front' => 0,
//         'visible_in_advanced_search' => 0,
//         'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//     ]
// );

// $installer->addAttribute(
//     Ccc_Vendor_Model_Product::ENTITY,
//     'vendor_id',
//     [
//         'group' => 'Common',
//         'input' => 'text',
//         'type' => 'varchar',
//         'label' => 'Vendor Id',
//         'backend' => '',
//         'visible' => 1,
//         'source' => 'eav/entity_attribute_source_table',
//         'required' => 1,
//         'user_defined' => 0,
//         'searchable' => 0,
//         'filterable' => 0,
//         'comparable' => 0,
//         'visible_on_front' => 0,
//         'visible_in_advanced_search' => 0,
//         'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//     ]
// );

// $installer->addAttribute(
//     Ccc_Vendor_Model_Product::ENTITY,
//     'vendor_product_approved',
//     [
//         'group' => 'Common',
//         'input' => 'select',
//         'type' => 'varchar',
//         'label' => '',
//         'backend' => '',
//         'visible' => 1,
//         'source' => 'eav/entity_attribute_source_table',
//         'option' => [
//             'values' => [
//                 'approved' => 'Apporved',
//                 'pending' => 'Pending',
//                 'rejected' => 'Rejected',
//             ],
//         ],
//         'source' => 'eav/entity_attribute_source_table',
//         'required' => 1,
//         'user_defined' => 0,
//         'searchable' => 0,
//         'filterable' => 0,
//         'comparable' => 0,
//         'visible_on_front' => 0,
//         'visible_in_advanced_search' => 0,
//         'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//     ]
// );

$installer->endSetup();
