<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute(
    Ccc_Vendor_Model_Product::ENTITY,
    'vendor_id',
    [
        'group' => 'General',
        'input' => 'text',
        'type' => 'varchar',
        'label' => 'Vendor_Id',
        'backend' => '',
        'visible' => 0,
        'required' => 0,
        'user_defined' => 0,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search' => 0,
        'global' => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ]
);

$installer->endSetup();
